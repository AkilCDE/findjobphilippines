<?php

namespace App\Http\Controllers;

use App\Models\JobApplication;
use App\Models\JobPost;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class WorkerController extends Controller
{
    /**
     * Display all applications submitted by the logged-in worker.
     */
    public function applications(): View
    {
        $user = Auth::user();

        $applications = JobApplication::where('worker_id', $user->id)
            ->with(['jobPost.employer'])
            ->latest()
            ->paginate(10);

        $totalApplications = $applications->total();
        $hiredCount = JobApplication::where('worker_id', $user->id)->where('status', 'hired')->count();
        $pendingCount = JobApplication::where('worker_id', $user->id)->where('status', 'pending')->count();
        $rejectedCount = JobApplication::where('worker_id', $user->id)->where('status', 'rejected')->count();

        return view('worker.applications', compact(
            'applications',
            'totalApplications',
            'hiredCount',
            'pendingCount',
            'rejectedCount'
        ));
    }

    /**
     * Apply for a job post and submit specific requirements.
     */
    public function apply(Request $request, JobPost $jobPost): RedirectResponse
    {
        $user = Auth::user();

        if (! $user->isWorker()) {
            return back()->with('error', 'Only registered worker accounts can apply for job postings.');
        }

        // Check if post is locked or archived
        if ($jobPost->isLocked() || $jobPost->isArchived()) {
            return back()->with('error', 'Sorry, this position has reached full worker capacity and is currently locked.');
        }

        // Check existing application
        $existing = JobApplication::where('job_post_id', $jobPost->id)
            ->where('worker_id', $user->id)
            ->first();

        if ($existing) {
            return back()->with('info', 'You have already applied for this job opportunity.');
        }

        $validated = $request->validate([
            'submitted_requirements' => ['required', 'string', 'min:5'],
            'cover_letter' => ['nullable', 'string', 'max:2000'],
            'attachment' => ['nullable', 'file', 'mimes:pdf,doc,docx,jpg,jpeg,png', 'max:5120'],
        ]);

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('applications', 'public');
        }

        JobApplication::create([
            'job_post_id' => $jobPost->id,
            'worker_id' => $user->id,
            'status' => 'pending',
            'submitted_requirements' => $validated['submitted_requirements'],
            'cover_letter' => $validated['cover_letter'] ?? null,
            'attachment_path' => $attachmentPath,
        ]);

        return redirect()->route('worker.applications')
            ->with('success', "Your application for '{$jobPost->title}' has been submitted successfully to {$jobPost->employer?->display_name}!");
    }

    /**
     * Cancel / withdraw a pending job application.
     */
    public function cancelApplication(JobApplication $application): RedirectResponse
    {
        $user = Auth::user();

        if ($application->worker_id !== $user->id && ! $user->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        if ($application->status === 'hired') {
            return back()->with('error', 'You cannot cancel an application that has already been hired.');
        }

        $application->delete();

        return back()->with('success', 'Your application has been withdrawn.');
    }
}
