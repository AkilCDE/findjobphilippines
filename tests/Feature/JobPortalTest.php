<?php

namespace Tests\Feature;

use App\Models\JobApplication;
use App\Models\JobPost;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JobPortalTest extends TestCase
{
    use RefreshDatabase;

    public function test_visitor_can_view_job_feed_and_map(): void
    {
        $response = $this->get('/jobs');
        $response->assertStatus(200);
        $response->assertSee('Findjob');

        $mapResponse = $this->get('/jobs/map');
        $mapResponse->assertStatus(200);
        $mapResponse->assertSee('Philippine Job Map');
    }

    public function test_worker_can_register_with_bohol_address(): void
    {
        $response = $this->post('/register', [
            'role' => 'worker',
            'first_name' => 'Juan',
            'last_name' => 'Dela Cruz',
            'sex' => 'Male',
            'email' => 'juan.worker@example.com',
            'address' => 'Ambuan, Catigbian, Bohol, Philippines',
            'contact_number' => '+63 912 345 6789',
            'password' => 'secret1234',
            'password_confirmation' => 'secret1234',
        ]);

        $response->assertRedirect('/jobs');
        $this->assertDatabaseHas('users', [
            'email' => 'juan.worker@example.com',
            'role' => 'worker',
            'first_name' => 'Juan',
            'last_name' => 'Dela Cruz',
            'address' => 'Ambuan, Catigbian, Bohol, Philippines',
        ]);
    }

    public function test_employer_can_post_job_with_map_coordinates(): void
    {
        $employer = User::factory()->create([
            'role' => 'employer',
            'company_name' => 'Catigbian Construction Corp',
        ]);

        $response = $this->actingAs($employer)->post('/employer/posts', [
            'title' => 'Backhoe Operator & Site Mason',
            'category' => 'Construction & Skilled Trades',
            'job_type' => 'Full-time',
            'salary_range' => '₱750 / day',
            'workers_needed' => 5,
            'location_address' => 'Ambuan, Catigbian, Bohol, Philippines',
            'latitude' => 9.8433,
            'longitude' => 124.0287,
            'requirements' => '1. TESDA NC II Certificate\n2. Barangay Clearance',
            'description' => 'Road concreting project in Ambuan, Catigbian.',
        ]);

        $response->assertRedirect(route('employer.posts.index'));
        $this->assertDatabaseHas('job_posts', [
            'title' => 'Backhoe Operator & Site Mason',
            'employer_id' => $employer->id,
            'workers_needed' => 5,
            'workers_hired' => 0,
            'status' => 'open',
            'latitude' => 9.8433,
            'longitude' => 124.0287,
        ]);
    }

    public function test_worker_can_apply_and_hiring_auto_locks_post_at_capacity(): void
    {
        $employer = User::factory()->create(['role' => 'employer']);
        $worker = User::factory()->create(['role' => 'worker']);

        $post = JobPost::create([
            'employer_id' => $employer->id,
            'title' => 'Urgent Electrician',
            'category' => 'Construction & Skilled Trades',
            'job_type' => 'Full-time',
            'salary_range' => '₱800 / day',
            'workers_needed' => 1, // Only 1 worker needed!
            'workers_hired' => 0,
            'location_address' => 'Tagbilaran City, Bohol',
            'latitude' => 9.6444,
            'longitude' => 123.8566,
            'status' => 'open',
            'requirements' => 'Valid PRC or TESDA License',
            'description' => 'Urgent commercial wiring.',
        ]);

        // Worker applies
        $applyResponse = $this->actingAs($worker)->post("/jobs/{$post->id}/apply", [
            'submitted_requirements' => 'Submitted: TESDA NC II Electrical Installation Card, Barangay Clearance.',
            'cover_letter' => 'Available to start right away.',
        ]);

        $applyResponse->assertRedirect(route('worker.applications'));
        $this->assertDatabaseHas('job_applications', [
            'job_post_id' => $post->id,
            'worker_id' => $worker->id,
            'status' => 'pending',
        ]);

        $application = JobApplication::where('job_post_id', $post->id)->first();

        // Employer hires the worker
        $hireResponse = $this->actingAs($employer)->post(
            "/employer/posts/{$post->id}/applicants/{$application->id}/hire"
        );

        $hireResponse->assertSessionHas('success');

        // Verify that post is now automatically locked because 1/1 hired!
        $post->refresh();
        $this->assertEquals(1, $post->workers_hired);
        $this->assertEquals('locked', $post->status);
        $this->assertTrue($post->isLocked());
    }

    public function test_admin_can_access_dashboard_and_manage_users(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'email' => 'admin.test@example.com',
        ]);

        $response = $this->actingAs($admin)->get('/admin/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Admin Command Dashboard');

        $usersResponse = $this->actingAs($admin)->get('/admin/users');
        $usersResponse->assertStatus(200);
        $usersResponse->assertSee('User Management');
    }
}
