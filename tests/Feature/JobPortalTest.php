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
            'password' => 'Secret1234!',
            'password_confirmation' => 'Secret1234!',
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
            'address' => 'Brgy. Luz, Cebu City, Cebu',
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

    public function test_employer_can_edit_own_job_post(): void
    {
        $employer = User::factory()->create(['role' => 'employer']);

        $post = JobPost::create([
            'employer_id' => $employer->id,
            'title' => 'Initial Title',
            'category' => 'Retail & Sales',
            'job_type' => 'Full-time',
            'salary_range' => '₱600 / day',
            'workers_needed' => 2,
            'workers_hired' => 0,
            'location_address' => 'Quezon City',
            'latitude' => 14.6760,
            'longitude' => 121.0437,
            'requirements' => 'Valid ID',
            'description' => 'Initial description',
            'status' => 'open',
        ]);

        // Verify employer sees Edit and Delete buttons on public show page
        $showResponse = $this->actingAs($employer)->get("/jobs/{$post->id}");
        $showResponse->assertStatus(200);
        $showResponse->assertSee('Edit Post');
        $showResponse->assertSee('Delete Post');

        // Verify employer sees Edit and Delete buttons on job catalog cards
        $jobsResponse = $this->actingAs($employer)->get('/jobs');
        $jobsResponse->assertStatus(200);
        $jobsResponse->assertSee('Edit');
        $jobsResponse->assertSee('Delete');

        // Verify employer sees Edit and Delete on applicants page
        $applicantsResponse = $this->actingAs($employer)->get("/employer/posts/{$post->id}/applicants");
        $applicantsResponse->assertStatus(200);
        $applicantsResponse->assertSee('Edit Post');
        $applicantsResponse->assertSee('Delete Post');

        // Visit edit page
        $editPageResponse = $this->actingAs($employer)->get("/employer/posts/{$post->id}/edit");
        $editPageResponse->assertStatus(200);
        $editPageResponse->assertSee('Edit Job Post');

        // Submit update
        $updateResponse = $this->actingAs($employer)->put("/employer/posts/{$post->id}", [
            'title' => 'Updated Retail Supervisor',
            'category' => 'Retail & Sales',
            'job_type' => 'Full-time',
            'salary_range' => '₱850 / day',
            'workers_needed' => 3,
            'location_address' => 'Quezon City Central',
            'latitude' => 14.6760,
            'longitude' => 121.0437,
            'requirements' => 'Valid ID, High School Diploma, NBI Clearance',
            'description' => 'Supervise retail floor operations.',
        ]);

        $updateResponse->assertRedirect(route('employer.posts.index'));
        $this->assertDatabaseHas('job_posts', [
            'id' => $post->id,
            'title' => 'Updated Retail Supervisor',
            'requirements' => 'Valid ID, High School Diploma, NBI Clearance',
            'workers_needed' => 3,
        ]);
    }

    public function test_employer_can_delete_own_job_post(): void
    {
        $employer = User::factory()->create(['role' => 'employer']);

        $post = JobPost::create([
            'employer_id' => $employer->id,
            'title' => 'Job To Delete',
            'category' => 'General Labor',
            'job_type' => 'Full-time',
            'workers_needed' => 1,
            'workers_hired' => 0,
            'location_address' => 'Davao City',
            'latitude' => 7.1907,
            'longitude' => 125.4553,
            'requirements' => 'Barangay Clearance',
            'description' => 'Short term work.',
            'status' => 'open',
        ]);

        $deleteResponse = $this->actingAs($employer)->delete("/employer/posts/{$post->id}");
        $deleteResponse->assertRedirect(route('employer.posts.index'));

        $this->assertDatabaseMissing('job_posts', [
            'id' => $post->id,
        ]);
    }

    public function test_employer_cannot_edit_or_delete_another_employer_job_post(): void
    {
        $employer1 = User::factory()->create(['role' => 'employer']);
        $employer2 = User::factory()->create(['role' => 'employer']);

        $post = JobPost::create([
            'employer_id' => $employer1->id,
            'title' => 'Employer 1 Post',
            'category' => 'General Labor',
            'job_type' => 'Full-time',
            'workers_needed' => 1,
            'workers_hired' => 0,
            'location_address' => 'Manila',
            'latitude' => 14.5995,
            'longitude' => 120.9842,
            'requirements' => 'Valid ID',
            'description' => 'Work details',
            'status' => 'open',
        ]);

        // Employer 2 attempts to edit Employer 1's post
        $editResponse = $this->actingAs($employer2)->get("/employer/posts/{$post->id}/edit");
        $editResponse->assertStatus(403);

        // Employer 2 attempts to update Employer 1's post
        $updateResponse = $this->actingAs($employer2)->put("/employer/posts/{$post->id}", [
            'title' => 'Hacked Title',
            'category' => 'General Labor',
            'job_type' => 'Full-time',
            'workers_needed' => 5,
            'location_address' => 'Manila',
            'latitude' => 14.5995,
            'longitude' => 120.9842,
            'requirements' => 'Valid ID',
            'description' => 'Work details',
        ]);
        $updateResponse->assertStatus(403);

        // Employer 2 attempts to delete Employer 1's post
        $deleteResponse = $this->actingAs($employer2)->delete("/employer/posts/{$post->id}");
        $deleteResponse->assertStatus(403);

        // Post should still exist in database unchanged
        $this->assertDatabaseHas('job_posts', [
            'id' => $post->id,
            'title' => 'Employer 1 Post',
        ]);
    }

    public function test_admin_sidebar_navbar_persists_on_messages_job_portal_and_philippine_map(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'email' => 'superadmin@example.com',
        ]);

        // 1. Direct Messages
        $msgResponse = $this->actingAs($admin)->get('/messages');
        $msgResponse->assertStatus(200);
        $msgResponse->assertSee('id="adminSidebar"', false);
        $msgResponse->assertSee('Admin Portal');

        // 2. Job Portal
        $jobsResponse = $this->actingAs($admin)->get('/jobs');
        $jobsResponse->assertStatus(200);
        $jobsResponse->assertSee('id="adminSidebar"', false);
        $jobsResponse->assertSee('Admin Portal');

        // 3. Philippine Map
        $mapResponse = $this->actingAs($admin)->get('/jobs/map');
        $mapResponse->assertStatus(200);
        $mapResponse->assertSee('id="adminSidebar"', false);
        $mapResponse->assertSee('Admin Portal');
    }

    public function test_admin_user_management_displays_separated_employer_and_worker_containers(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        User::factory()->create(['role' => 'employer', 'company_name' => 'Bohol Tech Ventures']);
        User::factory()->create(['role' => 'worker', 'first_name' => 'Pedro', 'last_name' => 'Penduko']);

        $response = $this->actingAs($admin)->get('/admin/users');
        $response->assertStatus(200);
        $response->assertSee('Registered Employers');
        $response->assertSee('Registered Workers');
        $response->assertSee('Bohol Tech Ventures');
        $response->assertSee('Pedro Penduko');
    }

    public function test_google_auth_accepts_gis_jwt_credential(): void
    {
        $header = rtrim(strtr(base64_encode(json_encode(['alg' => 'RS256', 'typ' => 'JWT'])), '+/', '-_'), '=');
        $payloadData = [
            'email' => 'device.google.user@gmail.com',
            'name' => 'Google Device User',
            'sub' => '1234567890',
        ];
        $payload = rtrim(strtr(base64_encode(json_encode($payloadData)), '+/', '-_'), '=');
        $signature = 'dummySignature';
        $dummyJwt = "{$header}.{$payload}.{$signature}";

        $response = $this->post('/auth/google', [
            'credential' => $dummyJwt,
            'google_role' => 'worker',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('google_setup', true);
        $this->assertTrue(str_contains($response->headers->get('Location'), 'device.google.user%40gmail.com') || str_contains($response->headers->get('Location'), 'device.google.user@gmail.com'));
        $this->assertGuest();
    }

    public function test_registration_trappings_validate_all_required_fields_and_strong_password(): void
    {
        // Weak password (no symbols/uppercase)
        $response = $this->post('/register', [
            'role' => 'worker',
            'first_name' => 'Test',
            'last_name' => 'Worker',
            'sex' => 'Male',
            'email' => 'new.worker@example.com',
            'address' => 'Brgy. San Isidro, Tagbilaran City, Bohol',
            'contact_number' => '09123456789',
            'password' => 'weakpass',
            'password_confirmation' => 'weakpass',
        ]);
        $response->assertSessionHasErrors(['password']);

        // Missing fields
        $missingResponse = $this->post('/register', [
            'role' => 'worker',
            'email' => '',
            'contact_number' => '',
            'password' => '',
        ]);
        $missingResponse->assertSessionHasErrors(['first_name', 'last_name', 'sex', 'email', 'contact_number', 'address', 'password']);
    }

    public function test_registration_trappings_prevent_duplicate_email(): void
    {
        User::factory()->create(['email' => 'existing.user@example.com']);

        $response = $this->post('/register', [
            'role' => 'worker',
            'first_name' => 'Juan',
            'last_name' => 'Luna',
            'sex' => 'Male',
            'email' => 'existing.user@example.com',
            'address' => 'Brgy. Poblacion, Catigbian, Bohol',
            'contact_number' => '09123456789',
            'password' => 'StrongPass123!',
            'password_confirmation' => 'StrongPass123!',
        ]);

        $response->assertSessionHasErrors(['email']);
    }

    public function test_registration_trappings_enforce_11_digit_ph_mobile_number(): void
    {
        // Short / invalid number
        $invalidResponse = $this->post('/register', [
            'role' => 'worker',
            'first_name' => 'Juan',
            'last_name' => 'Luna',
            'sex' => 'Male',
            'email' => 'valid.worker1@example.com',
            'address' => 'Brgy. Poblacion, Catigbian, Bohol',
            'contact_number' => '08123456789', // Not starting with 09
            'password' => 'StrongPass123!',
            'password_confirmation' => 'StrongPass123!',
        ]);
        $invalidResponse->assertSessionHasErrors(['contact_number']);

        // Valid 11-digit number starting with 09
        $validResponse = $this->post('/register', [
            'role' => 'worker',
            'first_name' => 'Juan',
            'last_name' => 'Luna',
            'sex' => 'Male',
            'email' => 'valid.worker1@example.com',
            'address' => 'Brgy. Poblacion, Catigbian, Bohol',
            'contact_number' => '09123456789',
            'password' => 'StrongPass123!',
            'password_confirmation' => 'StrongPass123!',
        ]);
        $validResponse->assertRedirect('/jobs');
        $this->assertDatabaseHas('users', [
            'email' => 'valid.worker1@example.com',
            'contact_number' => '09123456789',
        ]);
    }

    public function test_registration_trappings_enforce_full_address_format(): void
    {
        // Incomplete address (missing barangay/municipality/province format)
        $incompleteResponse = $this->post('/register', [
            'role' => 'worker',
            'first_name' => 'Pedro',
            'last_name' => 'Santos',
            'sex' => 'Male',
            'email' => 'pedro.santos@example.com',
            'address' => 'Bohol', // Only 1 part
            'contact_number' => '09123456789',
            'password' => 'StrongPass123!',
            'password_confirmation' => 'StrongPass123!',
        ]);
        $incompleteResponse->assertSessionHasErrors(['address']);

        // Full address format with 3 separate fields
        $completeResponse = $this->post('/register', [
            'role' => 'worker',
            'first_name' => 'Pedro',
            'last_name' => 'Santos',
            'sex' => 'Male',
            'email' => 'pedro.santos@example.com',
            'address_brgy' => 'Brgy. Ambuan',
            'address_city' => 'Catigbian',
            'address_province' => 'Bohol',
            'contact_number' => '09123456789',
            'password' => 'StrongPass123!',
            'password_confirmation' => 'StrongPass123!',
        ]);
        $completeResponse->assertRedirect('/jobs');
        $this->assertDatabaseHas('users', [
            'email' => 'pedro.santos@example.com',
            'address' => 'Brgy. Ambuan, Catigbian, Bohol',
        ]);
    }

    public function test_employer_registration_requires_and_saves_sex_and_names(): void
    {
        $response = $this->post('/register', [
            'role' => 'employer',
            'first_name' => 'Maria',
            'middle_name' => 'Clara',
            'last_name' => 'De Los Santos',
            'company_name' => 'Bohol Digital Innovations Inc.',
            'sex' => 'Female',
            'email' => 'recruiter.female@example.com',
            'address_brgy' => 'Brgy. Cogon',
            'address_city' => 'Tagbilaran City',
            'address_province' => 'Bohol',
            'contact_number' => '09987654321',
            'password' => 'EmployerSecure2026!',
            'password_confirmation' => 'EmployerSecure2026!',
        ]);

        $response->assertRedirect(route('employer.dashboard'));
        $this->assertDatabaseHas('users', [
            'email' => 'recruiter.female@example.com',
            'role' => 'employer',
            'first_name' => 'Maria',
            'middle_name' => 'Clara',
            'last_name' => 'De Los Santos',
            'company_name' => 'Bohol Digital Innovations Inc.',
            'sex' => 'Female',
            'contact_number' => '09987654321',
            'address' => 'Brgy. Cogon, Tagbilaran City, Bohol',
        ]);
    }

    public function test_landing_page_renders_with_enlarged_browse_button_and_tutorial(): void
    {
        $employer = User::factory()->create(['role' => 'employer']);
        JobPost::create([
            'employer_id' => $employer->id,
            'title' => 'Senior Welder Specialist',
            'category' => 'Construction & Skilled Trades',
            'job_type' => 'Full-time',
            'salary_range' => '₱900 / day',
            'workers_needed' => 3,
            'workers_hired' => 1,
            'location_address' => 'Tagbilaran City, Bohol',
            'latitude' => 9.6444,
            'longitude' => 123.8566,
            'status' => 'open',
            'requirements' => 'TESDA SMAW NC II',
            'description' => 'Heavy structural welding.',
        ]);

        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Browse All Jobs');
        $response->assertSee('How to Use Findjobph');
        $response->assertSee('For Job Seekers (Workers)');
        $response->assertSee('For Direct Employers');
        $response->assertSee('View Interactive Philippine Map');
    }

    public function test_guest_cannot_apply_for_job_and_must_sign_in(): void
    {
        $employer = User::factory()->create(['role' => 'employer']);
        $job = JobPost::create([
            'employer_id' => $employer->id,
            'title' => 'Master Carpenter Needed',
            'category' => 'Construction & Skilled Trades',
            'job_type' => 'Full-time',
            'salary_range' => '₱850 / day',
            'workers_needed' => 2,
            'workers_hired' => 0,
            'location_address' => 'Cebu City, Cebu',
            'latitude' => 10.3157,
            'longitude' => 123.8854,
            'status' => 'open',
            'requirements' => 'Woodworking experience',
            'description' => 'Custom furniture fabrication.',
        ]);

        // Jobs page shows the post with guest sign-in reminder
        $jobsResponse = $this->get('/jobs');
        $jobsResponse->assertStatus(200);
        $jobsResponse->assertSee('Master Carpenter Needed');
        $jobsResponse->assertSee('Sign in to Apply');

        // Job details page indicates guest authentication required
        $showResponse = $this->get("/jobs/{$job->id}");
        $showResponse->assertStatus(200);
        $showResponse->assertSee('Sign In to Apply for this Job');
        $showResponse->assertSee('Create Worker Account');
        $showResponse->assertDontSee('Submit Application with Verified Selfie');

        // Attempting to apply as an unauthenticated guest redirects to login
        $applyResponse = $this->post("/jobs/{$job->id}/apply", [
            'submitted_requirements' => 'Here are my valid credentials.',
        ]);
        $applyResponse->assertRedirect('/login');
    }

    public function test_admin_dashboard_renders_monochrome_dark_mode_and_interactive_charts(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get('/admin/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Admin Dashboard');
        $response->assertSee('mainActivityChart');
        $response->assertSee('userDonutChart');
        $response->assertSee('categoriesChart');
        $response->assertSee('applicationStatusChart');
        $response->assertSee('switchActivityMode');

        $postsResponse = $this->actingAs($admin)->get('/admin/posts');
        $postsResponse->assertStatus(200);
        $postsResponse->assertSee('Manage Job Postings');
    }

    public function test_all_pages_render_dark_mode_and_favicon_logo(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $worker = User::factory()->create(['role' => 'worker']);
        $employer = User::factory()->create(['role' => 'employer']);

        // 1. Landing page is in dark mode and uses favicon logo
        $homeResponse = $this->get('/');
        $homeResponse->assertStatus(200);
        $homeResponse->assertSee('favicon.svg');
        $homeResponse->assertSee('bg-[#09090b]');

        // 2. Browse jobs page is in dark mode
        $jobsResponse = $this->get('/jobs');
        $jobsResponse->assertStatus(200);
        $jobsResponse->assertSee('bg-[#09090b]');
        $jobsResponse->assertSee('bg-[#121215]');

        // 3. Map view uses CartoDB Dark Matter tiles
        $mapResponse = $this->get('/jobs/map');
        $mapResponse->assertStatus(200);
        $mapResponse->assertSee('basemaps.cartocdn.com/dark_all');
        $mapResponse->assertSee('bg-[#09090b]');

        // 4. Direct Messages page is in dark mode
        $messagesResponse = $this->actingAs($worker)->get('/messages?user_id='.$employer->id);
        $messagesResponse->assertStatus(200);
        $messagesResponse->assertSee('bg-[#121215]');
        $messagesResponse->assertSee('bg-[#18181b]');

        // 5. Admin portal displays circular favicon logo in sidebar
        $adminResponse = $this->actingAs($admin)->get('/admin/dashboard');
        $adminResponse->assertStatus(200);
        $adminResponse->assertSee('favicon.svg');
        $adminResponse->assertSee('Admin Portal');
    }

    public function test_worker_auto_generates_qr_code_with_pending_status_hiding_work_and_employer(): void
    {
        $worker = User::factory()->create([
            'role' => 'worker',
            'first_name' => 'Pedro',
            'last_name' => 'Penduko',
            'email' => 'pedro@example.com',
            'contact_number' => '09181234567',
            'sex' => 'Male',
            'address' => 'Tagbilaran City, Bohol',
        ]);

        $profileData = $worker->getQrProfileData();
        $this->assertEquals('pending', $profileData['status']);
        $this->assertNull($profileData['work_title']);
        $this->assertNull($profileData['employer_name']);

        // Check Applications page displays cleanly without the moved QR card
        $response = $this->actingAs($worker)->get('/worker/applications');
        $response->assertStatus(200);
        $response->assertDontSee('workerAppQrCode');

        // Check Profile page displays the worker QR code and pending status
        $profileResponse = $this->actingAs($worker)->get('/profile');
        $profileResponse->assertStatus(200);
        $profileResponse->assertSee('profileWorkerQr');
        $profileResponse->assertSee('Pending');

        // Check Digital ID pass page
        $passResponse = $this->actingAs($worker)->get('/worker/qr-pass');
        $passResponse->assertStatus(200);
        $passResponse->assertSee('Official Worker Digital ID Pass');
        $passResponse->assertSee('Pedro Penduko');
        $passResponse->assertSee('Pending');
        $passResponse->assertSee('fullPassQr');

        // Check public verify route
        $verifyResponse = $this->get('/qr/verify/'.$worker->qr_token);
        $verifyResponse->assertStatus(200);
        $verifyResponse->assertSee('Pedro Penduko');
        $verifyResponse->assertSee('Seeking Employment');
        $verifyResponse->assertDontSee('Active Work Position:');
    }

    public function test_worker_qr_code_updates_to_hired_displaying_work_title_and_employer_name(): void
    {
        $employer = User::factory()->create([
            'role' => 'employer',
            'company_name' => 'Super Mega Builders Corp',
        ]);
        $worker = User::factory()->create(['role' => 'worker', 'first_name' => 'Maria', 'last_name' => 'Clara']);

        $job = JobPost::create([
            'employer_id' => $employer->id,
            'title' => 'Lead Electrical Engineer',
            'category' => 'Engineering',
            'job_type' => 'Full-time',
            'salary_range' => '₱45,000 / month',
            'workers_needed' => 2,
            'workers_hired' => 1,
            'location_address' => 'Cebu City',
            'latitude' => 10.3157,
            'longitude' => 123.8854,
            'description' => 'Oversee electrical engineering tasks and infrastructure maintenance.',
            'requirements' => 'PRC License, Valid ID',
            'status' => 'open',
        ]);

        $app = JobApplication::create([
            'job_post_id' => $job->id,
            'worker_id' => $worker->id,
            'status' => 'hired',
            'submitted_requirements' => 'PRC License, Valid ID',
            'hired_at' => now(),
        ]);

        $profileData = $worker->getQrProfileData();
        $this->assertEquals('hired', $profileData['status']);
        $this->assertEquals('Lead Electrical Engineer', $profileData['work_title']);
        $this->assertEquals('Super Mega Builders Corp', $profileData['employer_name']);

        $verifyResponse = $this->get('/qr/verify/'.$worker->qr_token);
        $verifyResponse->assertStatus(200);
        $verifyResponse->assertSee('Hired');
        $verifyResponse->assertSee('Lead Electrical Engineer');
        $verifyResponse->assertSee('Super Mega Builders Corp');
    }

    public function test_worker_qr_code_updates_to_fired_displaying_work_title_and_employer_name(): void
    {
        $employer = User::factory()->create([
            'role' => 'employer',
            'company_name' => 'Global Logistics Inc',
        ]);
        $worker = User::factory()->create(['role' => 'worker', 'first_name' => 'Crisostomo', 'last_name' => 'Ibarra']);

        $job = JobPost::create([
            'employer_id' => $employer->id,
            'title' => 'Warehouse Supervisor',
            'category' => 'Logistics',
            'job_type' => 'Full-time',
            'salary_range' => '₱25,000 / month',
            'workers_needed' => 1,
            'workers_hired' => 0,
            'location_address' => 'Manila, Philippines',
            'latitude' => 14.5995,
            'longitude' => 120.9842,
            'description' => 'Manage inventory, safety, and team schedules in main warehouse.',
            'requirements' => 'Resume, Clearance',
            'status' => 'open',
        ]);

        JobApplication::create([
            'job_post_id' => $job->id,
            'worker_id' => $worker->id,
            'status' => 'fired',
            'submitted_requirements' => 'Resume, Clearance',
            'hired_at' => now()->subMonths(3),
            'ended_at' => now(),
            'termination_reason' => 'Attendance policy violation',
        ]);

        $profileData = $worker->getQrProfileData();
        $this->assertEquals('fired', $profileData['status']);
        $this->assertEquals('Warehouse Supervisor', $profileData['work_title']);
        $this->assertEquals('Global Logistics Inc', $profileData['employer_name']);
        $this->assertEquals('Attendance policy violation', $profileData['termination_reason']);

        $verifyResponse = $this->get('/qr/verify/'.$worker->qr_token);
        $verifyResponse->assertStatus(200);
        $verifyResponse->assertSee('Fired');
        $verifyResponse->assertSee('Warehouse Supervisor');
        $verifyResponse->assertSee('Global Logistics Inc');
        $verifyResponse->assertSee('Attendance policy violation');
    }

    public function test_employer_can_scan_worker_qr_code_and_receives_live_details(): void
    {
        $employer = User::factory()->create([
            'role' => 'employer',
            'company_name' => 'Tech Horizons',
        ]);
        $otherEmployer = User::factory()->create([
            'role' => 'employer',
            'company_name' => 'First Employer Inc',
        ]);
        $worker = User::factory()->create([
            'role' => 'worker',
            'first_name' => 'Elias',
            'last_name' => 'Salome',
            'email' => 'elias@example.com',
            'contact_number' => '09998887777',
        ]);

        // 1. Initial pending state scan
        $scanResponse = $this->actingAs($employer)->postJson('/employer/scan-worker-qr', [
            'code' => $worker->qr_token,
        ]);

        $scanResponse->assertStatus(200);
        $scanResponse->assertJson([
            'success' => true,
            'worker' => [
                'worker_id' => $worker->id,
                'name' => 'Elias Salome',
                'status' => 'pending',
                'work_title' => null,
                'employer_name' => null,
            ],
        ]);

        // 2. Worker gets hired
        $job = JobPost::create([
            'employer_id' => $otherEmployer->id,
            'title' => 'Heavy Equipment Mechanic',
            'category' => 'Maintenance',
            'job_type' => 'Full-time',
            'salary_range' => '₱30,000 / month',
            'workers_needed' => 2,
            'workers_hired' => 1,
            'location_address' => 'Davao City',
            'latitude' => 7.1907,
            'longitude' => 125.4553,
            'description' => 'Perform diagnostics, preventative repairs, and maintenance on hydraulics.',
            'requirements' => 'TESDA NC II',
            'status' => 'open',
        ]);
        $app = JobApplication::create([
            'job_post_id' => $job->id,
            'worker_id' => $worker->id,
            'status' => 'hired',
            'submitted_requirements' => 'TESDA NC II',
            'hired_at' => now(),
        ]);

        // The EXACT same QR code is scanned again, and it automatically reflects 'hired'!
        $scanResponse2 = $this->actingAs($employer)->postJson('/employer/scan-worker-qr', [
            'code' => $worker->qr_token,
        ]);

        $scanResponse2->assertStatus(200);
        $scanResponse2->assertJson([
            'success' => true,
            'worker' => [
                'worker_id' => $worker->id,
                'status' => 'hired',
                'work_title' => 'Heavy Equipment Mechanic',
                'employer_name' => 'First Employer Inc',
            ],
        ]);

        // 3. Worker gets fired
        $app->update([
            'status' => 'fired',
            'ended_at' => now(),
            'termination_reason' => 'Repeated unexcused absences',
        ]);

        // The EXACT same QR code is scanned again, and it automatically reflects 'fired'!
        $scanResponse3 = $this->actingAs($employer)->postJson('/employer/scan-worker-qr', [
            'code' => $worker->qr_token,
        ]);

        $scanResponse3->assertStatus(200);
        $scanResponse3->assertJson([
            'success' => true,
            'worker' => [
                'worker_id' => $worker->id,
                'status' => 'fired',
                'work_title' => 'Heavy Equipment Mechanic',
                'employer_name' => 'First Employer Inc',
                'termination_reason' => 'Repeated unexcused absences',
            ],
        ]);

        // 4. Test employer scanner page loads with all controls
        $scannerPageResponse = $this->actingAs($employer)->get('/employer/scanner');
        $scannerPageResponse->assertStatus(200);
        $scannerPageResponse->assertSee('Worker QR Scanner');
        $scannerPageResponse->assertSee('employerQrReader');
        $scannerPageResponse->assertSee('workerScanPopUpModal');
    }

    public function test_guest_cannot_view_profile(): void
    {
        $response = $this->get('/profile');
        $response->assertRedirect('/login');
    }

    public function test_worker_can_view_profile_with_personal_info_and_live_qr_code(): void
    {
        $worker = User::factory()->create([
            'role' => 'worker',
            'first_name' => 'Juan',
            'last_name' => 'Dela Cruz',
            'sex' => 'Male',
            'contact_number' => '09123456789',
            'address' => 'Barangay Poblacion, Tagbilaran City, Bohol',
            'email' => 'juan.worker@example.com',
        ]);

        $response = $this->actingAs($worker)->get('/profile');
        $response->assertStatus(200);
        $response->assertSee('Juan Dela Cruz');
        $response->assertSee('09123456789');
        $response->assertSee('Barangay Poblacion');
        $response->assertSee('Tagbilaran City');
        $response->assertSee('Bohol');
        $response->assertSee('Worker Pass');
        $response->assertSee('profileWorkerQr');
        $response->assertSee('Edit Profile Information');
    }

    public function test_worker_can_update_profile_information(): void
    {
        $worker = User::factory()->create([
            'role' => 'worker',
            'first_name' => 'Pedro',
            'last_name' => 'Santos',
            'sex' => 'Male',
            'contact_number' => '09111111111',
            'address' => 'Old Brgy, Old City, Old Province',
        ]);

        $response = $this->actingAs($worker)->put('/profile', [
            'first_name' => 'Pedro Updated',
            'middle_name' => 'Garcia',
            'last_name' => 'Santos Jr',
            'sex' => 'Male',
            'contact_number' => '09998887777',
            'address_brgy' => 'Brgy. San Jose',
            'address_city' => 'Catigbian',
            'address_province' => 'Bohol',
        ]);

        $response->assertRedirect(route('profile.show'));
        $response->assertSessionHas('success');

        $worker->refresh();
        $this->assertSame('Pedro Updated', $worker->first_name);
        $this->assertSame('Garcia', $worker->middle_name);
        $this->assertSame('Santos Jr', $worker->last_name);
        $this->assertSame('09998887777', $worker->contact_number);
        $this->assertSame('Brgy. San Jose, Catigbian, Bohol', $worker->address);
    }

    public function test_employer_can_view_profile_with_company_info_and_no_qr_code(): void
    {
        $employer = User::factory()->create([
            'role' => 'employer',
            'company_name' => 'Bohol Builders Hub',
            'first_name' => 'Maria',
            'last_name' => 'Lim',
            'sex' => 'Female',
            'contact_number' => '09887766554',
            'address' => 'Brgy. Central, Tubigon, Bohol',
            'email' => 'maria.lim@builders.ph',
        ]);

        $response = $this->actingAs($employer)->get('/profile');
        $response->assertStatus(200);
        $response->assertSee('Bohol Builders Hub');
        $response->assertSee('Maria');
        $response->assertSee('Lim');
        $response->assertSee('09887766554');
        $response->assertSee('Employer Profile');
        // Crucial requirement: employer profile should NOT have the worker QR pass
        $response->assertDontSee('profileWorkerQr');
        $response->assertDontSee('Worker Pass');
    }

    public function test_employer_can_update_company_profile(): void
    {
        $employer = User::factory()->create([
            'role' => 'employer',
            'company_name' => 'Old Enterprise',
            'first_name' => 'Carlos',
            'last_name' => 'Reyes',
            'sex' => 'Male',
            'contact_number' => '09223344556',
            'address' => 'Old Brgy, Old City, Old Province',
        ]);

        $response = $this->actingAs($employer)->put('/profile', [
            'company_name' => 'New Apex Construction Corp',
            'first_name' => 'Carlos',
            'middle_name' => 'Tan',
            'last_name' => 'Reyes',
            'sex' => 'Male',
            'contact_number' => '09334455667',
            'address_brgy' => 'Brgy. Manga',
            'address_city' => 'Tagbilaran City',
            'address_province' => 'Bohol',
        ]);

        $response->assertRedirect(route('profile.show'));
        $response->assertSessionHas('success');

        $employer->refresh();
        $this->assertSame('New Apex Construction Corp', $employer->company_name);
        $this->assertSame('Carlos', $employer->first_name);
        $this->assertSame('09334455667', $employer->contact_number);
        $this->assertSame('Brgy. Manga, Tagbilaran City, Bohol', $employer->address);
    }
}
