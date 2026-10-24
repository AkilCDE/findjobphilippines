<?php

namespace Database\Seeders;

use App\Models\JobApplication;
use App\Models\JobPost;
use App\Models\Message;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Admin User
        $admin = User::updateOrCreate(
            ['email' => 'admin@findjobph.com'],
            [
                'name' => 'System Administrator',
                'first_name' => 'System',
                'last_name' => 'Administrator',
                'role' => 'admin',
                'password' => Hash::make('password'),
                'contact_number' => '+63 900 000 0001',
                'address' => 'Makati City, Metro Manila, Philippines',
                'email_verified_at' => now(),
            ]
        );

        // 2. Primary Employer: Bohol Agro-Tourism & Builders
        $employer1 = User::updateOrCreate(
            ['email' => 'employer@findjobph.com'],
            [
                'name' => 'Bohol Agro-Tourism & Builders Corp.',
                'company_name' => 'Bohol Agro-Tourism & Builders Corp.',
                'role' => 'employer',
                'password' => Hash::make('password'),
                'contact_number' => '+63 917 888 1234',
                'address' => 'Poblacion, Catigbian, Bohol, Philippines',
                'email_verified_at' => now(),
            ]
        );

        // 3. Secondary Employers
        $employer2 = User::updateOrCreate(
            ['email' => 'cebu.employer@findjobph.com'],
            [
                'name' => 'Visayas Logistics & Tech Solutions',
                'company_name' => 'Visayas Logistics & Tech Solutions',
                'role' => 'employer',
                'password' => Hash::make('password'),
                'contact_number' => '+63 918 555 9876',
                'address' => 'Cebu IT Park, Apas, Cebu City, Philippines',
                'email_verified_at' => now(),
            ]
        );

        $employer3 = User::updateOrCreate(
            ['email' => 'manila.employer@findjobph.com'],
            [
                'name' => 'Megawide Capital Engineering',
                'company_name' => 'Megawide Capital Engineering',
                'role' => 'employer',
                'password' => Hash::make('password'),
                'contact_number' => '+63 920 333 4455',
                'address' => 'BGC, Taguig City, Metro Manila, Philippines',
                'email_verified_at' => now(),
            ]
        );

        // 4. Primary Worker
        $worker1 = User::updateOrCreate(
            ['email' => 'worker@findjobph.com'],
            [
                'name' => 'Juan Dela Cruz',
                'first_name' => 'Juan',
                'last_name' => 'Dela Cruz',
                'sex' => 'Male',
                'role' => 'worker',
                'password' => Hash::make('password'),
                'contact_number' => '+63 929 111 2233',
                'address' => 'Ambuan, Catigbian, Bohol, Philippines',
                'email_verified_at' => now(),
            ]
        );

        // 5. Additional Workers
        $worker2 = User::updateOrCreate(
            ['email' => 'maria@findjobph.com'],
            [
                'name' => 'Maria Santos',
                'first_name' => 'Maria',
                'last_name' => 'Santos',
                'sex' => 'Female',
                'role' => 'worker',
                'password' => Hash::make('password'),
                'contact_number' => '+63 945 222 3344',
                'address' => 'Tagbilaran City, Bohol, Philippines',
                'email_verified_at' => now(),
            ]
        );

        $worker3 = User::updateOrCreate(
            ['email' => 'ricardo@findjobph.com'],
            [
                'name' => 'Ricardo Dalisay',
                'first_name' => 'Ricardo',
                'last_name' => 'Dalisay',
                'sex' => 'Male',
                'role' => 'worker',
                'password' => Hash::make('password'),
                'contact_number' => '+63 939 444 5566',
                'address' => 'Subangdaku, Mandaue City, Cebu, Philippines',
                'email_verified_at' => now(),
            ]
        );

        // 6. Realistic Job Posts with Philippine Map Coordinates
        $job1 = JobPost::updateOrCreate(
            ['title' => 'Heavy Equipment Operator & Skilled Mason', 'employer_id' => $employer1->id],
            [
                'category' => 'Construction & Skilled Trades',
                'job_type' => 'Full-time',
                'salary_range' => '₱650 - ₱900 / day',
                'workers_needed' => 10,
                'workers_hired' => 2, // 2/10 hired
                'location_address' => 'Ambuan, Catigbian, Bohol, Philippines',
                'latitude' => 9.8433,
                'longitude' => 124.0287,
                'status' => 'open',
                'requirements' => "1. Valid Driver's License or TESDA NC II Heavy Equipment Certificate\n2. At least 1-2 years experience in site roadworks\n3. Barangay Clearance & Police Clearance\n4. Fit to work medical certificate",
                'description' => 'We are urgently seeking 10 hardworking operators and masons for the Catigbian municipal farm-to-market road concreting project. Free lodging, daily allowances, and complete safety equipment provided.',
            ]
        );

        $job2 = JobPost::updateOrCreate(
            ['title' => 'Organic Farming Assistants & Harvesters', 'employer_id' => $employer1->id],
            [
                'category' => 'Agriculture & Farming',
                'job_type' => 'Contract',
                'salary_range' => '₱550 - ₱700 / day',
                'workers_needed' => 5,
                'workers_hired' => 5, // 5/5 hired -> FULL / LOCKED!
                'location_address' => 'Sagbayan, Bohol, Philippines',
                'latitude' => 9.9167,
                'longitude' => 124.1000,
                'status' => 'locked',
                'requirements' => "1. Basic farming or agricultural background\n2. Willingness to work outdoors\n3. Barangay Certificate of Residency",
                'description' => 'Seasonal organic cacao and dragonfruit harvesting team. Note: This post is completely filled and currently locked.',
            ]
        );

        $job3 = JobPost::updateOrCreate(
            ['title' => 'Logistics Warehouse Crew & Dispatcher', 'employer_id' => $employer2->id],
            [
                'category' => 'Logistics & Delivery',
                'job_type' => 'Full-time',
                'salary_range' => '₱18,000 - ₱24,000 / month',
                'workers_needed' => 8,
                'workers_hired' => 4, // 4/8 hired
                'location_address' => 'Mandaue City, Cebu, Philippines',
                'latitude' => 10.3396,
                'longitude' => 123.9404,
                'status' => 'open',
                'requirements' => "1. High school graduate or vocational certificate\n2. Ability to lift 25kg packages\n3. SSS, PhilHealth, Pag-IBIG numbers ready\n4. Drug test clearance",
                'description' => 'Fast-growing e-commerce fulfillment hub in Mandaue seeking active warehouse crew. Benefits include health insurance, overtime pay, and 13th-month bonus.',
            ]
        );

        $job4 = JobPost::updateOrCreate(
            ['title' => 'Customer Service Representatives (CSR)', 'employer_id' => $employer2->id],
            [
                'category' => 'IT & Tech Support',
                'job_type' => 'Full-time',
                'salary_range' => '₱22,000 - ₱28,000 / month',
                'workers_needed' => 6,
                'workers_hired' => 1, // 1/6 hired
                'location_address' => 'Cebu IT Park, Apas, Cebu City, Philippines',
                'latitude' => 10.3275,
                'longitude' => 123.9064,
                'status' => 'open',
                'requirements' => "1. Conversational English skills\n2. Basic computer literacy\n3. High school or college undergraduate welcome\n4. Resume with updated contact information",
                'description' => 'Join our global accounts team at Cebu IT Park! Night shift differential, HMO upon regularization, and modern facilities with sleeping quarters.',
            ]
        );

        $job5 = JobPost::updateOrCreate(
            ['title' => 'Site Civil Engineer & Project Supervisors', 'employer_id' => $employer3->id],
            [
                'category' => 'Construction & Skilled Trades',
                'job_type' => 'Full-time',
                'salary_range' => '₱35,000 - ₱50,000 / month',
                'workers_needed' => 4,
                'workers_hired' => 2, // 2/4 hired
                'location_address' => 'Bonifacio Global City, Taguig, Metro Manila, Philippines',
                'latitude' => 14.5547,
                'longitude' => 121.0493,
                'status' => 'open',
                'requirements' => "1. Licensed Civil Engineer (PRC)\n2. Minimum 3 years high-rise construction experience\n3. AutoCAD and BIM proficiency\n4. Professional references",
                'description' => 'High-rise residential tower project in BGC Taguig. Responsible for site safety, structural QA/QC, and contractor coordination.',
            ]
        );

        $job6 = JobPost::updateOrCreate(
            ['title' => 'Resort Front Office & Housekeeping Staff', 'employer_id' => $employer1->id],
            [
                'category' => 'Hospitality & Tourism',
                'job_type' => 'Full-time',
                'salary_range' => '₱16,000 - ₱20,000 + Service Charge',
                'workers_needed' => 6,
                'workers_hired' => 3, // 3/6 hired
                'location_address' => 'Alona Beach, Panglao, Bohol, Philippines',
                'latitude' => 9.5786,
                'longitude' => 123.7719,
                'status' => 'open',
                'requirements' => "1. Friendly, hospitable attitude with pleasant personality\n2. Experience in hotel or resort operations is a plus\n3. Barangay Clearance and Valid Gov't ID",
                'description' => 'Premier eco-resort in Panglao seeking dynamic front desk receptionists and meticulous housekeeping team members.',
            ]
        );

        $job7 = JobPost::updateOrCreate(
            ['title' => 'Commercial Delivery Van Drivers', 'employer_id' => $employer2->id],
            [
                'category' => 'Logistics & Delivery',
                'job_type' => 'Full-time',
                'salary_range' => '₱750 - ₱950 / day + Meal Allowance',
                'workers_needed' => 4,
                'workers_hired' => 1,
                'location_address' => 'Davao City, Davao del Sur, Philippines',
                'latitude' => 7.1907,
                'longitude' => 125.4553,
                'status' => 'open',
                'requirements' => "1. Professional Driver's License with code 1,2 or B/B1/B2\n2. Clean driving record without major violations\n3. Familiar with Mindanao delivery corridors",
                'description' => 'Delivery van drivers for FMCG route delivery across Davao Region. Clean trucks, fuel card, and accident insurance provided.',
            ]
        );

        $job8 = JobPost::updateOrCreate(
            ['title' => 'Pastry Chef & Bread Baker Assistants', 'employer_id' => $employer3->id],
            [
                'category' => 'Hospitality & Tourism',
                'job_type' => 'Full-time',
                'salary_range' => '₱18,000 - ₱23,000 / month',
                'workers_needed' => 2,
                'workers_hired' => 2, // 2/2 hired -> LOCKED!
                'location_address' => 'Session Road, Baguio City, Benguet, Philippines',
                'latitude' => 16.4023,
                'longitude' => 120.5960,
                'status' => 'locked',
                'requirements' => "1. Culinary or baking background\n2. Food Safety & Sanitary Permit\n3. Punctual and early morning availability",
                'description' => 'Heritage Baguio cafe baking artisan sourdough, strawberry pastries, and specialty breads. Capacity is filled.',
            ]
        );

        // 7. Seed Applications
        JobApplication::updateOrCreate(
            ['job_post_id' => $job1->id, 'worker_id' => $worker1->id],
            [
                'status' => 'hired',
                'hired_at' => now()->subDays(2),
                'cover_letter' => 'Good day Sir/Maam! I have 4 years experience operating backhoes and concrete mixers here in Bohol. Ready to start immediately.',
                'submitted_requirements' => "Submitted: TESDA NC II Certificate, Catigbian Barangay Clearance, PhilHealth ID, Driver's License Code 2.",
            ]
        );

        JobApplication::updateOrCreate(
            ['job_post_id' => $job1->id, 'worker_id' => $worker2->id],
            [
                'status' => 'hired',
                'hired_at' => now()->subDay(),
                'cover_letter' => 'Experienced skilled mason with expertise in stone cladding and drainage works. Residing nearby in Tagbilaran.',
                'submitted_requirements' => 'Submitted: Valid Voter ID, Barangay Residency Certificate, Health Examination form.',
            ]
        );

        JobApplication::updateOrCreate(
            ['job_post_id' => $job1->id, 'worker_id' => $worker3->id],
            [
                'status' => 'pending',
                'cover_letter' => 'Applying as heavy equipment operator. I have completed several DPWH bypass projects.',
                'submitted_requirements' => 'Submitted: Resume copy, TESDA Operator Card, Police Clearance.',
            ]
        );

        JobApplication::updateOrCreate(
            ['job_post_id' => $job3->id, 'worker_id' => $worker3->id],
            [
                'status' => 'hired',
                'hired_at' => now()->subDays(5),
                'cover_letter' => 'Applying for warehouse crew position in Mandaue. I live 10 minutes away from the facility.',
                'submitted_requirements' => 'Submitted: High School Diploma, NBI Clearance, Medical Fit-to-Work cert.',
            ]
        );

        // 8. Seed Direct Messages between Worker and Employer
        Message::updateOrCreate(
            ['sender_id' => $worker1->id, 'receiver_id' => $employer1->id, 'job_post_id' => $job1->id],
            [
                'message' => 'Good day Sir! I submitted my TESDA certificate and Barangay Clearance for the Heavy Equipment Operator post in Ambuan, Catigbian.',
                'is_read' => true,
                'created_at' => now()->subDays(2)->subHours(3),
            ]
        );

        Message::updateOrCreate(
            ['sender_id' => $employer1->id, 'receiver_id' => $worker1->id, 'job_post_id' => $job1->id],
            [
                'message' => 'Hello Juan! We reviewed your credentials. You have been HIRED as part of our Catigbian road team. Please report to the site office this Monday at 7:30 AM.',
                'is_read' => true,
                'created_at' => now()->subDays(2)->subHour(),
            ]
        );

        Message::updateOrCreate(
            ['sender_id' => $worker1->id, 'receiver_id' => $employer1->id, 'job_post_id' => $job1->id],
            [
                'message' => 'Thank you very much Sir! I will be there early with my safety boots and hard hat ready.',
                'is_read' => false,
                'created_at' => now()->subHours(10),
            ]
        );
    }
}
