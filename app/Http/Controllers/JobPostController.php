<?php

namespace App\Http\Controllers;

use App\Models\JobPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class JobPostController extends Controller
{
    /**
     * Display a listing of job opportunities with comprehensive filters.
     */
    public function index(Request $request): View
    {
        $query = JobPost::with(['employer'])
            ->active();

        // Keyword search (title, description, requirements, location)
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('requirements', 'like', "%{$search}%")
                    ->orWhere('location_address', 'like', "%{$search}%");
            });
        }

        // Category filter
        if ($category = $request->input('category')) {
            $query->where('category', $category);
        }

        // Job type filter
        if ($jobType = $request->input('job_type')) {
            $query->where('job_type', $jobType);
        }

        // Location / Island group filter
        if ($location = $request->input('location')) {
            $query->where('location_address', 'like', "%{$location}%");
        }

        // Status filter: default to open jobs only, or allows viewing all
        $availability = $request->input('availability', 'open');
        if ($availability === 'open') {
            $query->where('status', 'open');
        } elseif ($availability === 'locked') {
            $query->where('status', 'locked');
        }

        // Sorting
        $sort = $request->input('sort', 'latest');
        if ($sort === 'salary_high') {
            $query->orderByDesc('salary_range');
        } elseif ($sort === 'capacity') {
            $query->orderByRaw('(workers_needed - workers_hired) DESC');
        } else {
            $query->latest();
        }

        $jobs = $query->paginate(9)->withQueryString();

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

        $popularLocations = [
            'Bohol',
            'Cebu',
            'Metro Manila',
            'Davao',
            'Iloilo',
            'Pampanga',
            'Baguio',
            'Batangas',
            'Cagayan de Oro',
        ];

        return view('jobs.index', compact('jobs', 'categories', 'popularLocations'));
    }

    /**
     * Display the full Philippine Map view showing all pinned job locations.
     */
    public function map(Request $request): View
    {
        $query = JobPost::with('employer')->active();

        if ($category = $request->input('category')) {
            $query->where('category', $category);
        }

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('location_address', 'like', "%{$search}%");
            });
        }

        $jobs = $query->get()->map(function ($job) {
            return [
                'id' => $job->id,
                'title' => $job->title,
                'category' => $job->category,
                'job_type' => $job->job_type,
                'salary_range' => $job->salary_range,
                'location_address' => $job->location_address,
                'latitude' => (float) $job->latitude,
                'longitude' => (float) $job->longitude,
                'workers_needed' => $job->workers_needed,
                'workers_hired' => $job->workers_hired,
                'status' => $job->status,
                'is_locked' => $job->isLocked(),
                'employer_name' => $job->employer?->display_name ?? 'Verified Employer',
                'detail_url' => route('jobs.show', $job),
            ];
        });

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

        return view('jobs.map', compact('jobs', 'categories'));
    }

    /**
     * Display a specific job post detail.
     */
    public function show(JobPost $jobPost): View
    {
        // If archived, only admin or the owning employer can view it
        if ($jobPost->isArchived()) {
            $user = Auth::user();
            if (! $user || (! $user->isAdmin() && $user->id !== $jobPost->employer_id)) {
                abort(404, 'Job post is no longer available.');
            }
        }

        $jobPost->load(['employer', 'applications.worker']);

        $user = Auth::user();
        $userApplication = null;
        if ($user && $user->isWorker()) {
            $userApplication = $jobPost->applications()
                ->where('worker_id', $user->id)
                ->first();
        }

        return view('jobs.show', compact('jobPost', 'userApplication'));
    }
}
