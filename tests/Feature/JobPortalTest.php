<?php

namespace Tests\Feature;

use App\Models\JobApplication;
use App\Models\JobPost;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
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

        Storage::fake('public');
        $file = UploadedFile::fake()->image('tesda_card.jpg');
        $base64Selfie = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==';

        // Worker applies
        $applyResponse = $this->actingAs($worker)->post("/jobs/{$post->id}/apply", [
            'submitted_requirements' => 'Submitted: TESDA NC II Electrical Installation Card, Barangay Clearance.',
            'cover_letter' => 'Available to start right away.',
            'requirements_photo' => $file,
            'selfie_data' => $base64Selfie,
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
        $response->assertSee('Admin Dashboard');

        $usersResponse = $this->actingAs($admin)->get('/admin/users');
        $usersResponse->assertStatus(200);
        $usersResponse->assertSee('User Management');
    }

    public function test_employer_can_fire_worker_and_slot_automatically_reopens(): void
    {
        $employer = User::factory()->create(['role' => 'employer']);
        $worker = User::factory()->create(['role' => 'worker']);

        $post = JobPost::create([
            'employer_id' => $employer->id,
            'title' => 'Warehouse Stacker',
            'category' => 'Logistics & Delivery',
            'job_type' => 'Full-time',
            'salary_range' => '₱600 / day',
            'workers_needed' => 1,
            'workers_hired' => 1,
            'status' => 'locked',
            'location_address' => 'Cebu City',
            'latitude' => 10.3157,
            'longitude' => 123.8854,
            'requirements' => 'Physically fit',
            'description' => 'Heavy lifting and organizing.',
        ]);

        $application = JobApplication::create([
            'job_post_id' => $post->id,
            'worker_id' => $worker->id,
            'status' => 'hired',
            'submitted_requirements' => 'Barangay clearance, Medical certificate',
            'hired_at' => now(),
        ]);

        $this->assertTrue($post->isLocked());
        $this->assertTrue($worker->hasActiveJob());

        // Employer fires the worker
        $fireResponse = $this->actingAs($employer)->post(
            "/employer/posts/{$post->id}/applicants/{$application->id}/fire",
            ['reason' => 'Repeated tardiness']
        );

        $fireResponse->assertSessionHas('success');

        $application->refresh();
        $this->assertEquals('fired', $application->status);
        $this->assertNotNull($application->ended_at);

        // Verify post unlocked and worker count decremented
        $post->refresh();
        $this->assertEquals(0, $post->workers_hired);
        $this->assertEquals('open', $post->status);
        $this->assertFalse($post->isLocked());

        // Worker no longer has active job and can apply elsewhere
        $worker->refresh();
        $this->assertFalse($worker->hasActiveJob());
    }

    public function test_worker_with_active_job_cannot_apply_for_another_job(): void
    {
        $employer1 = User::factory()->create(['role' => 'employer']);
        $employer2 = User::factory()->create(['role' => 'employer']);
        $worker = User::factory()->create(['role' => 'worker']);

        // First job where worker is actively hired
        $job1 = JobPost::create([
            'employer_id' => $employer1->id,
            'title' => 'Security Guard',
            'category' => 'General Labor',
            'job_type' => 'Full-time',
            'workers_needed' => 2,
            'workers_hired' => 1,
            'status' => 'open',
            'location_address' => 'Tagbilaran City',
            'latitude' => 9.6444,
            'longitude' => 123.8566,
            'requirements' => 'Security License',
            'description' => 'Day shift guard.',
        ]);

        JobApplication::create([
            'job_post_id' => $job1->id,
            'worker_id' => $worker->id,
            'status' => 'hired',
            'submitted_requirements' => 'Security ID',
            'hired_at' => now(),
        ]);

        $this->assertTrue($worker->hasActiveJob());

        // Second job that worker tries to apply for
        $job2 = JobPost::create([
            'employer_id' => $employer2->id,
            'title' => 'Driver',
            'category' => 'Logistics & Delivery',
            'job_type' => 'Full-time',
            'workers_needed' => 1,
            'workers_hired' => 0,
            'status' => 'open',
            'location_address' => 'Dauis, Bohol',
            'latitude' => 9.6200,
            'longitude' => 123.8600,
            'requirements' => 'Drivers License',
            'description' => 'Company van driver.',
        ]);

        // Attempt to apply should fail due to active job trapping
        $applyResponse = $this->actingAs($worker)->post("/jobs/{$job2->id}/apply", [
            'submitted_requirements' => 'Professional Drivers License',
        ]);

        $applyResponse->assertSessionHas('error');
        $this->assertDatabaseMissing('job_applications', [
            'job_post_id' => $job2->id,
            'worker_id' => $worker->id,
        ]);
    }

    public function test_worker_can_request_leave_and_employer_confirmation_releases_worker(): void
    {
        $employer = User::factory()->create(['role' => 'employer']);
        $worker = User::factory()->create(['role' => 'worker']);

        $job = JobPost::create([
            'employer_id' => $employer->id,
            'title' => 'Cashier',
            'category' => 'Retail & Sales',
            'job_type' => 'Full-time',
            'workers_needed' => 1,
            'workers_hired' => 1,
            'status' => 'locked',
            'location_address' => 'Panglao, Bohol',
            'latitude' => 9.5794,
            'longitude' => 123.7544,
            'requirements' => 'High school diploma',
            'description' => 'Store cashier.',
        ]);

        $application = JobApplication::create([
            'job_post_id' => $job->id,
            'worker_id' => $worker->id,
            'status' => 'hired',
            'submitted_requirements' => 'Diploma, IDs',
            'hired_at' => now(),
        ]);

        // Worker requests leave / quit
        $leaveResponse = $this->actingAs($worker)->post(
            "/worker/applications/{$application->id}/request-leave",
            ['leave_reason' => 'Moving to another province']
        );

        $leaveResponse->assertSessionHas('success');

        $application->refresh();
        $this->assertEquals('leave_requested', $application->status);
        $this->assertNotNull($application->leave_requested_at);

        // Employer confirms leave
        $confirmResponse = $this->actingAs($employer)->post(
            "/employer/posts/{$job->id}/applicants/{$application->id}/confirm-leave"
        );

        $confirmResponse->assertSessionHas('success');

        $application->refresh();
        $this->assertEquals('resigned', $application->status);
        $this->assertNotNull($application->ended_at);

        // Post capacity reopened and unlocked
        $job->refresh();
        $this->assertEquals(0, $job->workers_hired);
        $this->assertEquals('open', $job->status);

        // Worker is now released and can apply for new jobs
        $worker->refresh();
        $this->assertFalse($worker->hasActiveJob());
    }

    public function test_employer_cannot_apply_for_another_employer_job_and_can_direct_message(): void
    {
        $employer1 = User::factory()->create(['role' => 'employer']);
        $employer2 = User::factory()->create(['role' => 'employer']);

        $job = JobPost::create([
            'employer_id' => $employer1->id,
            'title' => 'Project Engineer',
            'category' => 'Construction & Skilled Trades',
            'job_type' => 'Contract',
            'workers_needed' => 2,
            'workers_hired' => 0,
            'status' => 'open',
            'location_address' => 'Cebu IT Park',
            'latitude' => 10.3297,
            'longitude' => 123.9060,
            'requirements' => 'Civil Engineering degree',
            'description' => 'Commercial site oversight.',
        ]);

        // Employer 2 browses details of Employer 1's post
        $showResponse = $this->actingAs($employer2)->get("/jobs/{$job->id}");
        $showResponse->assertStatus(200);
        $showResponse->assertSee('Employer-to-Employer Overview');
        $showResponse->assertDontSee('Submit Application');

        // Employer 2 attempts to apply via POST endpoint
        $applyResponse = $this->actingAs($employer2)->post("/jobs/{$job->id}/apply", [
            'submitted_requirements' => 'Engineering License',
        ]);

        // Forbidden by worker role middleware or controller restriction
        $this->assertDatabaseMissing('job_applications', [
            'job_post_id' => $job->id,
            'worker_id' => $employer2->id,
        ]);

        // Employer 2 can direct message Employer 1
        $msgResponse = $this->actingAs($employer2)->post('/messages', [
            'receiver_id' => $employer1->id,
            'job_post_id' => $job->id,
            'message' => 'Hello, our construction firm is open for sub-contracting collaboration.',
        ]);

        $msgResponse->assertRedirect(route('messages.index', ['user_id' => $employer1->id]));
        $this->assertDatabaseHas('messages', [
            'sender_id' => $employer2->id,
            'receiver_id' => $employer1->id,
            'job_post_id' => $job->id,
        ]);
    }

    public function test_employer_post_creation_requires_mandatory_requirements(): void
    {
        $employer = User::factory()->create(['role' => 'employer']);

        // Attempt to create without requirements
        $response = $this->actingAs($employer)->post('/employer/posts', [
            'title' => 'Retail Cashier',
            'category' => 'Retail & Sales',
            'job_type' => 'Full-time',
            'salary_range' => '₱650 / day',
            'workers_needed' => 1,
            'location_address' => 'Makati City',
            'latitude' => 14.5547,
            'longitude' => 121.0244,
            'description' => 'Supermarket cashier.',
            // Missing requirements
        ]);

        $response->assertSessionHasErrors('requirements');

        // Successful creation with mandatory requirements
        $validResponse = $this->actingAs($employer)->post('/employer/posts', [
            'title' => 'Retail Cashier',
            'category' => 'Retail & Sales',
            'job_type' => 'Full-time',
            'salary_range' => '₱650 / day',
            'workers_needed' => 1,
            'location_address' => 'Makati City',
            'latitude' => 14.5547,
            'longitude' => 121.0244,
            'description' => 'Supermarket cashier.',
            'requirements' => 'Barangay Clearance, Valid Government ID (UMID/Passport/PhilID)',
        ]);

        $validResponse->assertRedirect(route('employer.posts.index'));
        $this->assertDatabaseHas('job_posts', [
            'title' => 'Retail Cashier',
            'requirements' => 'Barangay Clearance, Valid Government ID (UMID/Passport/PhilID)',
        ]);
    }

    public function test_worker_application_mandates_requirement_photo_and_selfie(): void
    {
        Storage::fake('public');

        $employer = User::factory()->create(['role' => 'employer']);
        $worker = User::factory()->create(['role' => 'worker']);

        $job = JobPost::create([
            'employer_id' => $employer->id,
            'title' => 'Delivery Rider',
            'category' => 'Logistics & Delivery',
            'job_type' => 'Full-time',
            'workers_needed' => 2,
            'workers_hired' => 0,
            'status' => 'open',
            'location_address' => 'Pasig City',
            'latitude' => 14.5764,
            'longitude' => 121.0851,
            'requirements' => "Driver's License, NBI Clearance",
            'description' => 'Motorcycle delivery within Metro Manila.',
        ]);

        // 1. Missing requirement photo and selfie
        $failResponse = $this->actingAs($worker)->post("/jobs/{$job->id}/apply", [
            'submitted_requirements' => "Driver's License attached",
        ]);

        $failResponse->assertSessionHasErrors(['requirements_photo', 'selfie_file']);

        // 2. Complete submission with requirement photo file & live webcam base64 selfie
        $file = UploadedFile::fake()->image('license.jpg');
        // 1x1 transparent PNG base64
        $base64Selfie = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==';

        $successResponse = $this->actingAs($worker)->post("/jobs/{$job->id}/apply", [
            'submitted_requirements' => "Valid Professional Driver's License and Barangay Clearance",
            'requirements_photo' => $file,
            'selfie_data' => $base64Selfie,
        ]);

        $successResponse->assertRedirect(route('worker.applications'));
        $this->assertDatabaseHas('job_applications', [
            'job_post_id' => $job->id,
            'worker_id' => $worker->id,
        ]);

        $application = JobApplication::where('job_post_id', $job->id)
            ->where('worker_id', $worker->id)
            ->first();

        $this->assertNotNull($application->attachment_path);
        $this->assertNotNull($application->selfie_path);
    }

    public function test_google_auth_redirects_unregistered_users_to_complete_account_setup(): void
    {
        // When Google account does not exist in DB
        $response = $this->post('/auth/google', [
            'google_email' => 'maria.google.test@gmail.com',
            'google_name' => 'Maria Santos',
            'google_role' => 'worker',
        ]);

        // Should NOT direct to home page. Must redirect to register to set up account!
        $response->assertRedirect();
        $response->assertSessionHas('google_setup', true);
        $this->assertTrue(str_contains($response->headers->get('Location'), 'register'));
        $this->assertGuest();

        // Complete account setup on register
        $registerResponse = $this->post('/register', [
            'role' => 'worker',
            'first_name' => 'Maria',
            'last_name' => 'Santos',
            'email' => 'maria.google.test@gmail.com',
            'sex' => 'Female',
            'address' => 'Cebu City, Philippines',
            'contact_number' => '+63 917 123 4567',
            'password' => 'secretPassword123!',
            'password_confirmation' => 'secretPassword123!',
        ]);

        $registerResponse->assertRedirect(route('jobs.index'));
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'email' => 'maria.google.test@gmail.com',
            'name' => 'Maria Santos',
        ]);

        // Signing out
        $this->post('/logout');
        $this->assertGuest();

        // Subsequent sign-in with Google immediately authenticates already set up user
        $loginResponse = $this->post('/auth/google', [
            'google_email' => 'maria.google.test@gmail.com',
            'google_name' => 'Maria Santos',
        ]);

        $loginResponse->assertRedirect(route('jobs.index'));
        $this->assertAuthenticated();
    }
}
