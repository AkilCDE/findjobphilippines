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
        $hiredCount = JobApplication::where('worker_id', $user->id)->whereIn('status', ['hired', 'leave_requested'])->count();
        $pendingCount = JobApplication::where('worker_id', $user->id)->where('status', 'pending')->count();
        $rejectedCount = JobApplication::where('worker_id', $user->id)->whereIn('status', ['rejected', 'fired', 'resigned'])->count();
        $activeEngagement = $user->activeJobApplication();

        return view('worker.applications', compact(
            'applications',
            'totalApplications',
            'hiredCount',
            'pendingCount',
            'rejectedCount',
            'activeEngagement'
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

        // Trapping: Worker cannot apply for another job if already hired in an active job
        if ($user->hasActiveJob()) {
            $currentJob = $user->activeJobApplication()?->jobPost?->title ?? 'an active position';

            return back()->with('error', "You already have an active job ('{$currentJob}'). You cannot apply for another job until you leave or are released from your current position.");
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
            if ($existing->isActiveEngagement()) {
                return back()->with('info', 'You are already hired for this position.');
            }
            if ($existing->status === 'pending') {
                return back()->with('info', 'You have already applied for this job opportunity and your application is pending review.');
            }
            if ($existing->status === 'fired') {
                return back()->with('error', 'Your employment for this position was previously terminated by the employer. You cannot re-apply.');
            }

            // Re-apply if previously rejected or resigned
            $validated = $request->validate([
                'submitted_requirements' => ['required', 'string', 'min:5'],
                'cover_letter' => ['nullable', 'string', 'max:2000'],
                'attachment' => ['nullable', 'file', 'mimes:pdf,doc,docx,jpg,jpeg,png', 'max:5120'],
            ]);

            $attachmentPath = $existing->attachment_path;
            if ($request->hasFile('attachment')) {
                $attachmentPath = $request->file('attachment')->store('applications', 'public');
            }

            $existing->update([
                'status' => 'pending',
                'submitted_requirements' => $validated['submitted_requirements'],
                'cover_letter' => $validated['cover_letter'] ?? null,
                'attachment_path' => $attachmentPath,
                'leave_reason' => null,
                'leave_requested_at' => null,
                'termination_reason' => null,
                'ended_at' => null,
            ]);

            return redirect()->route('worker.applications')
                ->with('success', "Your application for '{$jobPost->title}' has been re-submitted successfully to {$jobPost->employer?->display_name}!");
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
     * Worker requests to leave / quit their current hired job.
     */
    public function requestLeave(Request $request, JobApplication $application): RedirectResponse
    {
        $user = Auth::user();

        if ($application->worker_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        if ($application->status !== 'hired') {
            return back()->with('error', 'You can only request leave for a position you are actively hired in.');
        }

        $validated = $request->validate([
            'leave_reason' => ['nullable', 'string', 'max:1000'],
        ]);

        $application->update([
            'status' => 'leave_requested',
            'leave_reason' => $validated['leave_reason'] ?? 'Worker requested to quit/leave position.',
            'leave_requested_at' => now(),
        ]);

        return back()->with('success', "Your leave request for '{$application->jobPost?->title}' has been sent to your employer for confirmation.");
    }

    /**
     * Worker cancels their pending leave request.
     */
    public function cancelLeave(JobApplication $application): RedirectResponse
    {
        $user = Auth::user();

        if ($application->worker_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        if ($application->status !== 'leave_requested') {
            return back()->with('error', 'No active leave request found to cancel.');
        }

        $application->update([
            'status' => 'hired',
            'leave_reason' => null,
            'leave_requested_at' => null,
        ]);

        return back()->with('success', 'Your leave request has been cancelled. You remain actively hired.');
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

        if ($application->isActiveEngagement()) {
            return back()->with('error', "You cannot directly withdraw an active position. Please click 'Request Leave / Quit Job' so your employer can confirm your departure.");
        }

        $application->delete();

        return back()->with('success', 'Your application has been withdrawn.');
    }
}
