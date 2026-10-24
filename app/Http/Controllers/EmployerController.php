<?php

namespace App\Http\Controllers;

use App\Models\JobApplication;
use App\Models\JobPost;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class EmployerController extends Controller
{
    /**
     * Display employer dashboard with statistics and active posts.
     */
    public function dashboard(): View
    {
        $user = Auth::user();

        $posts = JobPost::where('employer_id', $user->id)
            ->withCount('applications')
            ->latest()
            ->get();

        $totalPosts = $posts->count();
        $activePosts = $posts->where('status', 'open')->count();
        $lockedPosts = $posts->where('status', 'locked')->count();
        $archivedPosts = $posts->where('status', 'archived')->count();
        $totalHired = $posts->sum('workers_hired');
        $totalNeeded = $posts->sum('workers_needed');

        $pendingApplicationsCount = JobApplication::whereHas('jobPost', function ($q) use ($user) {
            $q->where('employer_id', $user->id);
        })->where('status', 'pending')->count();

        $pendingLeaveRequestsCount = JobApplication::whereHas('jobPost', function ($q) use ($user) {
            $q->where('employer_id', $user->id);
        })->where('status', 'leave_requested')->count();

        return view('employer.dashboard', compact(
            'posts',
            'totalPosts',
            'activePosts',
            'lockedPosts',
            'archivedPosts',
            'totalHired',
            'totalNeeded',
            'pendingApplicationsCount',
            'pendingLeaveRequestsCount'
        ));
    }

    /**
     * Display all posts belonging to the employer.
     */
    public function posts(Request $request): View
    {
        $user = Auth::user();
        $status = $request->query('status', 'all');

        $query = JobPost::where('employer_id', $user->id)
            ->withCount(['applications', 'hiredApplications']);

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $posts = $query->latest()->paginate(10)->withQueryString();

        return view('employer.posts.index', compact('posts', 'status'));
    }

    /**
     * Show the form to create a new job post with Philippine map selector.
     */
    public function create(): View
    {
        $categories = [
            'Construction & Skilled Trades',
            'Agriculture & Farming',
            'Hospitality & Tourism',
            'IT & Tech Support',
            'Logistics & Delivery',
            'Retail & Sales',
            'Healthcare & Caregiving',
            'Office & Admin',
            'Manufacturing & Factory',
            'General Labor',
        ];

        return view('employer.posts.create', compact('categories'));
    }

    /**
     * Store a new job post with location pin coordinates.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:100'],
            'job_type' => ['required', 'string', 'in:Full-time,Part-time,Contract,Daily Rate,Project-based'],
            'salary_range' => ['nullable', 'string', 'max:100'],
            'workers_needed' => ['required', 'integer', 'min:1', 'max:1000'],
            'location_address' => ['required', 'string', 'max:500'],
            'latitude' => ['required', 'numeric', 'between:4.0,22.0'],
            'longitude' => ['required', 'numeric', 'between:116.0,127.0'],
            'requirements' => ['required', 'string'],
            'description' => ['required', 'string'],
        ]);

        $jobPost = JobPost::create([
            'employer_id' => Auth::id(),
            'title' => $validated['title'],
            'category' => $validated['category'],
            'job_type' => $validated['job_type'],
            'salary_range' => $validated['salary_range'],
            'workers_needed' => $validated['workers_needed'],
            'workers_hired' => 0,
            'location_address' => $validated['location_address'],
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'requirements' => $validated['requirements'],
            'description' => $validated['description'],
            'status' => 'open',
        ]);

        return redirect()->route('employer.posts.index')
            ->with('success', "Job opportunity '{$jobPost->title}' has been successfully posted with location pinned on the Philippine map!");
    }

    /**
     * Show form to edit an existing job post.
     */
    public function edit(JobPost $jobPost): View
    {
        $this->authorizePost($jobPost);

        $categories = [
            'Construction & Skilled Trades',
            'Agriculture & Farming',
            'Hospitality & Tourism',
            'IT & Tech Support',
            'Logistics & Delivery',
            'Retail & Sales',
            'Healthcare & Caregiving',
            'Office & Admin',
            'Manufacturing & Factory',
            'General Labor',
        ];

        return view('employer.posts.edit', compact('jobPost', 'categories'));
    }

    /**
     * Update an existing job post.
     */
    public function update(Request $request, JobPost $jobPost): RedirectResponse
    {
        $this->authorizePost($jobPost);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:100'],
            'job_type' => ['required', 'string', 'in:Full-time,Part-time,Contract,Daily Rate,Project-based'],
            'salary_range' => ['nullable', 'string', 'max:100'],
            'workers_needed' => ['required', 'integer', "min:{$jobPost->workers_hired}", 'max:1000'],
            'location_address' => ['required', 'string', 'max:500'],
            'latitude' => ['required', 'numeric', 'between:4.0,22.0'],
            'longitude' => ['required', 'numeric', 'between:116.0,127.0'],
            'requirements' => ['required', 'string'],
            'description' => ['required', 'string'],
        ], [
            'workers_needed.min' => "Workers needed cannot be less than the {$jobPost->workers_hired} already hired.",
        ]);

        $jobPost->update($validated);
        $jobPost->syncLockStatus();

        return redirect()->route('employer.posts.index')
            ->with('success', "Job post '{$jobPost->title}' updated successfully.");
    }

    /**
     * Archive a job post (removes it from the worker feed).
     */
    public function archive(JobPost $jobPost): RedirectResponse
    {
        $this->authorizePost($jobPost);

        $jobPost->update([
            'status' => 'archived',
            'archived_at' => now(),
        ]);

        return back()->with('success', "Post '{$jobPost->title}' has been archived and will no longer appear on worker job feeds.");
    }

    /**
     * Unarchive a job post.
     */
    public function unarchive(JobPost $jobPost): RedirectResponse
    {
        $this->authorizePost($jobPost);

        $newStatus = ($jobPost->workers_hired >= $jobPost->workers_needed) ? 'locked' : 'open';

        $jobPost->update([
            'status' => $newStatus,
            'archived_at' => null,
        ]);

        return back()->with('success', "Post '{$jobPost->title}' is restored and active.");
    }

    /**
     * Delete a job post completely.
     */
    public function destroy(JobPost $jobPost): RedirectResponse
    {
        $this->authorizePost($jobPost);

        $title = $jobPost->title;
        $jobPost->delete();

        return redirect()->route('employer.posts.index')
            ->with('success', "Job post '{$title}' has been permanently deleted.");
    }

    /**
     * View all applicants for a specific job post.
     */
    public function applicants(JobPost $jobPost): View
    {
        $this->authorizePost($jobPost);

        $jobPost->load(['applications.worker']);

        return view('employer.posts.applicants', compact('jobPost'));
    }

    /**
     * Hire an applicant worker and auto-lock post if capacity reaches 100%.
     */
    public function hireApplicant(JobPost $jobPost, JobApplication $application): RedirectResponse
    {
        $this->authorizePost($jobPost);

        if ($application->job_post_id !== $jobPost->id) {
            abort(404);
        }

        if ($jobPost->workers_hired >= $jobPost->workers_needed) {
            return back()->with('error', "Cannot hire more workers. Post has reached maximum capacity ({$jobPost->workers_needed}/{$jobPost->workers_needed}) and is locked.");
        }

        if ($application->status === 'hired') {
            return back()->with('info', "Worker {$application->worker->display_name} is already hired.");
        }

        // Restriction: Verify applicant worker is not currently active in another position
        if ($application->worker && $application->worker->hasActiveJob() && ! $application->isActiveEngagement()) {
            return back()->with('error', "Worker {$application->worker->display_name} already holds another active job and cannot be hired until released from that position.");
        }

        $application->update([
            'status' => 'hired',
            'hired_at' => now(),
            'leave_reason' => null,
            'leave_requested_at' => null,
            'termination_reason' => null,
            'ended_at' => null,
        ]);

        $jobPost->increment('workers_hired');
        $jobPost->refresh();
        $jobPost->syncLockStatus();

        $lockNotice = ($jobPost->isLocked())
            ? " Maximum capacity reached ({$jobPost->workers_hired}/{$jobPost->workers_needed})! Post is now automatically LOCKED and no longer accepting applications."
            : " Current hired: {$jobPost->workers_hired}/{$jobPost->workers_needed}.";

        return back()->with('success', "Worker {$application->worker->display_name} has been marked as HIRED!{$lockNotice}");
    }

    /**
     * Fire / terminate a worker from this job post and automatically kick them.
     */
    public function fireWorker(Request $request, JobPost $jobPost, JobApplication $application): RedirectResponse
    {
        $this->authorizePost($jobPost);

        if ($application->job_post_id !== $jobPost->id) {
            abort(404);
        }

        if (! $application->isActiveEngagement()) {
            return back()->with('error', 'This worker is not currently active in this position.');
        }

        $workerName = $application->worker?->display_name ?? 'Worker';

        $application->update([
            'status' => 'fired',
            'termination_reason' => $request->input('reason', 'Terminated and kicked by employer.'),
            'ended_at' => now(),
        ]);

        if ($jobPost->workers_hired > 0) {
            $jobPost->decrement('workers_hired');
            $jobPost->refresh();
            $jobPost->syncLockStatus();
        }

        return back()->with('success', "Worker {$workerName} has been fired and automatically kicked from this job. The hiring slot has reopened ({$jobPost->workers_hired}/{$jobPost->workers_needed} filled).");
    }

    /**
     * Confirm a worker's request to leave / quit the job.
     */
    public function confirmLeave(JobPost $jobPost, JobApplication $application): RedirectResponse
    {
        $this->authorizePost($jobPost);

        if ($application->job_post_id !== $jobPost->id) {
            abort(404);
        }

        if ($application->status !== 'leave_requested') {
            return back()->with('error', 'No pending leave request found for this worker.');
        }

        $workerName = $application->worker?->display_name ?? 'Worker';

        $application->update([
            'status' => 'resigned',
            'ended_at' => now(),
        ]);

        if ($jobPost->workers_hired > 0) {
            $jobPost->decrement('workers_hired');
            $jobPost->refresh();
            $jobPost->syncLockStatus();
        }

        return back()->with('success', "Leave request for {$workerName} has been approved. The worker has been released from this job and the slot is reopened ({$jobPost->workers_hired}/{$jobPost->workers_needed} filled).");
    }

    /**
     * Decline a worker's request to leave.
     */
    public function rejectLeave(JobPost $jobPost, JobApplication $application): RedirectResponse
    {
        $this->authorizePost($jobPost);

        if ($application->job_post_id !== $jobPost->id) {
            abort(404);
        }

        if ($application->status !== 'leave_requested') {
            return back()->with('error', 'No pending leave request found for this worker.');
        }

        $workerName = $application->worker?->display_name ?? 'Worker';

        $application->update([
            'status' => 'hired',
            'leave_reason' => null,
            'leave_requested_at' => null,
        ]);

        return back()->with('info', "Leave request for {$workerName} was declined. The worker remains active.");
    }

    /**
     * Reject an applicant worker.
     */
    public function rejectApplicant(JobPost $jobPost, JobApplication $application): RedirectResponse
    {
        $this->authorizePost($jobPost);

        if ($application->job_post_id !== $jobPost->id) {
            abort(404);
        }

        // If previously hired, decrement hired count
        if ($application->isActiveEngagement() && $jobPost->workers_hired > 0) {
            $jobPost->decrement('workers_hired');
            $jobPost->refresh();
            $jobPost->syncLockStatus();
        }

        $application->update([
            'status' => 'rejected',
        ]);

        return back()->with('success', "Application from {$application->worker->display_name} has been set to Rejected.");
    }

    /**
     * Authorize that the current user owns this job post (or is admin).
     */
    protected function authorizePost(JobPost $jobPost): void
    {
        $user = Auth::user();
        if (! $user->isAdmin() && $user->id !== $jobPost->employer_id) {
            abort(403, 'Unauthorized access to this job post.');
        }
    }
}
