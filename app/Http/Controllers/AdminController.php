<?php

namespace App\Http\Controllers;

use App\Models\JobApplication;
use App\Models\JobPost;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AdminController extends Controller
{
    /**
     * Display the Admin command center dashboard.
     */
    public function dashboard(): View
    {
        $totalUsers = User::count();
        $workersCount = User::where('role', 'worker')->count();
        $employersCount = User::where('role', 'employer')->count();
        $adminsCount = User::where('role', 'admin')->count();

        $totalPosts = JobPost::count();
        $openPosts = JobPost::where('status', 'open')->count();
        $lockedPosts = JobPost::where('status', 'locked')->count();
        $archivedPosts = JobPost::where('status', 'archived')->count();

        $totalApplications = JobApplication::count();
        $totalHired = JobApplication::where('status', 'hired')->count();
        $totalMessages = Message::count();

        $recentUsers = User::latest()->take(5)->get();
        $recentPosts = JobPost::with('employer')->latest()->take(5)->get();

        // 1. Activity Trend: Last 7 days applications and hires
        $trendDates = [];
        $trendApplications = [];
        $trendHires = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dateStr = $date->format('M d');
            $trendDates[] = $dateStr;
            $trendApplications[] = JobApplication::whereDate('created_at', $date->toDateString())->count();
            $trendHires[] = JobApplication::where('status', 'hired')
                ->whereDate('hired_at', $date->toDateString())
                ->count();
        }

        // 2. Job Posts by Category
        $categoryData = JobPost::selectRaw('category, count(*) as count')
            ->groupBy('category')
            ->orderByDesc('count')
            ->take(6)
            ->pluck('count', 'category')
            ->toArray();

        // 3. Application Status Breakdown
        $applicationStatusData = [
            'Hired' => $totalHired,
            'Pending' => JobApplication::where('status', 'pending')->count(),
            'Leave Requested' => JobApplication::where('status', 'leave_requested')->count(),
            'Resigned' => JobApplication::where('status', 'resigned')->count(),
            'Fired' => JobApplication::where('status', 'fired')->count(),
            'Rejected' => JobApplication::where('status', 'rejected')->count(),
        ];

        return view('admin.dashboard', compact(
            'totalUsers',
            'workersCount',
            'employersCount',
            'adminsCount',
            'totalPosts',
            'openPosts',
            'lockedPosts',
            'archivedPosts',
            'totalApplications',
            'totalHired',
            'totalMessages',
            'recentUsers',
            'recentPosts',
            'trendDates',
            'trendApplications',
            'trendHires',
            'categoryData',
            'applicationStatusData'
        ));
    }

    /**
     * Display all users with search, role filter, and sorting.
     */
    public function users(Request $request): View
    {
        $query = User::withCount(['jobPosts', 'applications']);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('contact_number', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%")
                    ->orWhere('company_name', 'like', "%{$search}%");
            });
        }

        if ($role = $request->input('role')) {
            if ($role !== 'all') {
                $query->where('role', $role);
            }
        }

        $sort = $request->input('sort', 'latest');
        if ($sort === 'name') {
            $query->orderBy('name');
        } elseif ($sort === 'role') {
            $query->orderBy('role')->orderBy('name');
        } else {
            $query->latest();
        }

        $users = $query->paginate(12)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    /**
     * Show form to edit user details.
     */
    public function editUser(User $user): View
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update user details.
     */
    public function updateUser(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', "unique:users,email,{$user->id}"],
            'role' => ['required', 'in:admin,worker,employer'],
            'first_name' => ['nullable', 'string', 'max:100'],
            'last_name' => ['nullable', 'string', 'max:100'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'sex' => ['nullable', 'string', 'in:Male,Female,Other,Prefer not to say'],
            'contact_number' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:500'],
        ]);

        $user->update($validated);

        return redirect()->route('admin.users.index')
            ->with('success', "User '{$user->display_name}' has been successfully updated.");
    }

    /**
     * Delete user with complete confirmation.
     */
    public function deleteUser(User $user): RedirectResponse
    {
        if ($user->id === Auth::id()) {
            return back()->with('error', 'Security restriction: You cannot delete your own admin account.');
        }

        $name = $user->display_name;
        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', "User '{$name}' and all associated records have been permanently removed.");
    }

    /**
     * Manage all job posts sorted by users / employers.
     */
    public function posts(Request $request): View
    {
        $query = JobPost::with('employer')
            ->withCount('applications');

        // Filter by specific employer
        if ($employerId = $request->input('employer_id')) {
            $query->where('employer_id', $employerId);
        }

        // Status filter
        if ($status = $request->input('status')) {
            if ($status !== 'all') {
                $query->where('status', $status);
            }
        }

        // Search query
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('location_address', 'like', "%{$search}%")
                    ->orWhereHas('employer', function ($emp) use ($search) {
                        $emp->where('name', 'like', "%{$search}%")
                            ->orWhere('company_name', 'like', "%{$search}%");
                    });
            });
        }

        // Sort: by employer, by capacity, or by newest
        $sort = $request->input('sort', 'employer');
        if ($sort === 'employer') {
            $query->join('users', 'job_posts.employer_id', '=', 'users.id')
                ->select('job_posts.*')
                ->orderBy('users.name');
        } elseif ($sort === 'capacity') {
            $query->orderByRaw('(workers_needed - workers_hired) DESC');
        } elseif ($sort === 'workers_needed') {
            $query->orderByDesc('workers_needed');
        } else {
            $query->latest('job_posts.created_at');
        }

        $posts = $query->paginate(12)->withQueryString();

        $employers = User::where('role', 'employer')
            ->has('jobPosts')
            ->orderBy('name')
            ->get();

        return view('admin.posts.index', compact('posts', 'employers'));
    }

    /**
     * Delete job post by admin with complete confirmation.
     */
    public function deletePost(JobPost $jobPost): RedirectResponse
    {
        $title = $jobPost->title;
        $jobPost->delete();

        return back()->with('success', "Job post '{$title}' has been successfully deleted by Admin.");
    }

    /**
     * Toggle status or lock state for any post.
     */
    public function toggleStatus(JobPost $jobPost): RedirectResponse
    {
        if ($jobPost->status === 'open') {
            $jobPost->update(['status' => 'locked']);
            $msg = "Post '{$jobPost->title}' is now LOCKED.";
        } else {
            $jobPost->update(['status' => 'open', 'archived_at' => null]);
            $msg = "Post '{$jobPost->title}' is now OPEN.";
        }

        return back()->with('success', $msg);
    }
}
