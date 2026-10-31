<?php

namespace Database\Seeders;

use App\Models\JobApplication;
use App\Models\JobPost;
use App\Models\Message;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database with authentic Filipino profiles and 20 geographic jobs.
     */
    public function run(): void
    {
        // 0. Clean slate: remove all existing data in proper dependency order
        $driver = DB::getDriverName();
        if ($driver === 'pgsql') {
            DB::statement('TRUNCATE TABLE messages, job_applications, job_posts, users RESTART IDENTITY CASCADE');
        } else {
            Schema::disableForeignKeyConstraints();
            DB::table('messages')->truncate();
            DB::table('job_applications')->truncate();
            DB::table('job_posts')->truncate();
            DB::table('users')->truncate();
            Schema::enableForeignKeyConstraints();
        }

        // ==========================================
        // 1. ADMIN USER
        // ==========================================
        $admin = User::create([
            'name' => 'Danilo Alcantara',
            'first_name' => 'Danilo',
            'middle_name' => 'Reyes',
            'last_name' => 'Alcantara',
            'email' => 'admin@findjobph.com',
            'role' => 'admin',
            'sex' => 'Male',
            'password' => Hash::make(env('ADMIN_PASSWORD', 'findjobphadmin123')),
            'contact_number' => '09171234567',
            'address' => 'Poblacion, Makati City, Metro Manila',
            'email_verified_at' => now(),
        ]);

        // ==========================================
        // 2. FILIPINO EMPLOYERS (Personal Name in `name`, Company separated in `company_name`)
        // ==========================================

        // LUZON EMPLOYERS
        $empLuzon1 = User::create([
            'name' => 'Carlos Antonio Mendoza',
            'first_name' => 'Carlos',
            'middle_name' => 'Antonio',
            'last_name' => 'Mendoza',
            'company_name' => 'Megawide Capital Engineering & Builders',
            'email' => 'manila.employer@findjobph.com',
            'role' => 'employer',
            'sex' => 'Male',
            'password' => Hash::make('password'),
            'contact_number' => '09173334455',
            'address' => 'Fort Bonifacio, Taguig City, Metro Manila',
            'email_verified_at' => now(),
        ]);

        $empLuzon2 = User::create([
            'name' => 'Eduardo Dimayuga',
            'first_name' => 'Eduardo',
            'middle_name' => 'Perez',
            'last_name' => 'Dimayuga',
            'company_name' => 'Central Luzon Industrial & Agro Builders Co.',
            'email' => 'pampanga.employer@findjobph.com',
            'role' => 'employer',
            'sex' => 'Male',
            'password' => Hash::make('password'),
            'contact_number' => '09184445566',
            'address' => 'Dolores, San Fernando City, Pampanga',
            'email_verified_at' => now(),
        ]);

        $empLuzon3 = User::create([
            'name' => 'Marivic Soriano',
            'first_name' => 'Marivic',
            'middle_name' => 'Bautista',
            'last_name' => 'Soriano',
            'company_name' => 'Cordillera Highlands Agro & Hospitality Corp.',
            'email' => 'baguio.employer@findjobph.com',
            'role' => 'employer',
            'sex' => 'Female',
            'password' => Hash::make('password'),
            'contact_number' => '09205556677',
            'address' => 'Session Road, Baguio City, Benguet',
            'email_verified_at' => now(),
        ]);

        // VISAYAS EMPLOYERS
        $empVisayas1 = User::create([
            'name' => 'Roberto Santos',
            'first_name' => 'Roberto',
            'middle_name' => 'Gomez',
            'last_name' => 'Santos',
            'company_name' => 'Bohol Agro-Tourism & Builders Corp.',
            'email' => 'employer@findjobph.com',
            'role' => 'employer',
            'sex' => 'Male',
            'password' => Hash::make('password'),
            'contact_number' => '09178881234',
            'address' => 'Poblacion, Catigbian, Bohol',
            'email_verified_at' => now(),
        ]);

        $empVisayas2 = User::create([
            'name' => 'Maria Elena Ramos',
            'first_name' => 'Maria Elena',
            'middle_name' => 'Castillo',
            'last_name' => 'Ramos',
            'company_name' => 'Visayas Logistics & Cold Chain Solutions',
            'email' => 'cebu.employer@findjobph.com',
            'role' => 'employer',
            'sex' => 'Female',
            'password' => Hash::make('password'),
            'contact_number' => '09185559876',
            'address' => 'Lahug, Cebu City, Cebu',
            'email_verified_at' => now(),
        ]);

        $empVisayas3 = User::create([
            'name' => 'Teresa Macapagal',
            'first_name' => 'Teresa',
            'middle_name' => 'Lacson',
            'last_name' => 'Macapagal',
            'company_name' => 'Panay Maritime Transport & Trading Services',
            'email' => 'iloilo.employer@findjobph.com',
            'role' => 'employer',
            'sex' => 'Female',
            'password' => Hash::make('password'),
            'contact_number' => '09196667788',
            'address' => 'Mandurriao, Iloilo City, Iloilo',
            'email_verified_at' => now(),
        ]);

        // MINDANAO EMPLOYERS
        $empMindanao1 = User::create([
            'name' => 'Rodrigo Tan',
            'first_name' => 'Rodrigo',
            'middle_name' => 'Alonzo',
            'last_name' => 'Tan',
            'company_name' => 'Mindanao Fresh Harvest & Logistics Inc.',
            'email' => 'davao.employer@findjobph.com',
            'role' => 'employer',
            'sex' => 'Male',
            'password' => Hash::make('password'),
            'contact_number' => '09177778899',
            'address' => 'Buhangin, Davao City, Davao del Sur',
            'email_verified_at' => now(),
        ]);

        $empMindanao2 = User::create([
            'name' => 'Corazon Villafuerte',
            'first_name' => 'Corazon',
            'middle_name' => 'Navarro',
            'last_name' => 'Villafuerte',
            'company_name' => 'General Santos Marine Canning & Cold Storage',
            'email' => 'gensan.employer@findjobph.com',
            'role' => 'employer',
            'sex' => 'Female',
            'password' => Hash::make('password'),
            'contact_number' => '09218889900',
            'address' => 'Tambler, General Santos City, South Cotabato',
            'email_verified_at' => now(),
        ]);

        $empMindanao3 = User::create([
            'name' => 'Felipe Magbanua',
            'first_name' => 'Felipe',
            'middle_name' => 'Cruz',
            'last_name' => 'Magbanua',
            'company_name' => 'Northern Mindanao Industrial Builders',
            'email' => 'cdo.employer@findjobph.com',
            'role' => 'employer',
            'sex' => 'Male',
            'password' => Hash::make('password'),
            'contact_number' => '09229990011',
            'address' => 'Lapasan, Cagayan de Oro City, Misamis Oriental',
            'email_verified_at' => now(),
        ]);

        // Developer employer accounts
        $empDev1 = User::create([
            'name' => 'Johnley De Castro',
            'first_name' => 'Johnley',
            'middle_name' => null,
            'last_name' => 'De Castro',
            'company_name' => 'De Castro Enterprises',
            'email' => 'javabrocode@gmail.com',
            'role' => 'employer',
            'sex' => 'Male',
            'password' => Hash::make('password'),
            'contact_number' => '09123456789',
            'address' => 'Poblacion, Taguig City, Metro Manila',
            'email_verified_at' => now(),
        ]);

        // ==========================================
        // 3. FILIPINO WORKERS
        // ==========================================
        $worker1 = User::create([
            'name' => 'Juan Miguel Dela Cruz',
            'first_name' => 'Juan Miguel',
            'middle_name' => 'Bautista',
            'last_name' => 'Dela Cruz',
            'email' => 'worker@findjobph.com',
            'role' => 'worker',
            'sex' => 'Male',
            'password' => Hash::make('password'),
            'contact_number' => '09291112233',
            'address' => 'Ambuan, Catigbian, Bohol',
            'email_verified_at' => now(),
        ]);

        $worker2 = User::create([
            'name' => 'Maria Lourdes Santos',
            'first_name' => 'Maria Lourdes',
            'middle_name' => 'Gomez',
            'last_name' => 'Santos',
            'email' => 'maria@findjobph.com',
            'role' => 'worker',
            'sex' => 'Female',
            'password' => Hash::make('password'),
            'contact_number' => '09452223344',
            'address' => 'Poblacion, Tagbilaran City, Bohol',
            'email_verified_at' => now(),
        ]);

        $worker3 = User::create([
            'name' => 'Ricardo Jose Dalisay',
            'first_name' => 'Ricardo Jose',
            'middle_name' => 'Cortez',
            'last_name' => 'Dalisay',
            'email' => 'ricardo@findjobph.com',
            'role' => 'worker',
            'sex' => 'Male',
            'password' => Hash::make('password'),
            'contact_number' => '09394445566',
            'address' => 'Subangdaku, Mandaue City, Cebu',
            'email_verified_at' => now(),
        ]);

        $worker4 = User::create([
            'name' => 'Bayani Macaraeg',
            'first_name' => 'Bayani',
            'middle_name' => 'Castro',
            'last_name' => 'Macaraeg',
            'email' => 'bayani@findjobph.com',
            'role' => 'worker',
            'sex' => 'Male',
            'password' => Hash::make('password'),
            'contact_number' => '09283334455',
            'address' => 'San Fernando, San Fernando City, Pampanga',
            'email_verified_at' => now(),
        ]);

        $worker5 = User::create([
            'name' => 'Crisanto Reyes',
            'first_name' => 'Crisanto',
            'middle_name' => 'Manalo',
            'last_name' => 'Reyes',
            'email' => 'crisanto@findjobph.com',
            'role' => 'worker',
            'sex' => 'Male',
            'password' => Hash::make('password'),
            'contact_number' => '09355556677',
            'address' => 'Parian, Calamba City, Laguna',
            'email_verified_at' => now(),
        ]);

        $worker6 = User::create([
            'name' => 'Marilou Bautista',
            'first_name' => 'Marilou',
            'middle_name' => 'Flores',
            'last_name' => 'Bautista',
            'email' => 'marilou@findjobph.com',
            'role' => 'worker',
            'sex' => 'Female',
            'password' => Hash::make('password'),
            'contact_number' => '09477778899',
            'address' => 'Session Road, Baguio City, Benguet',
            'email_verified_at' => now(),
        ]);

        $worker7 = User::create([
            'name' => 'Jayson Dimaculangan',
            'first_name' => 'Jayson',
            'middle_name' => 'Pascual',
            'last_name' => 'Dimaculangan',
            'email' => 'jayson@findjobph.com',
            'role' => 'worker',
            'sex' => 'Male',
            'password' => Hash::make('password'),
            'contact_number' => '09129998877',
            'address' => 'Buhangin, Davao City, Davao del Sur',
            'email_verified_at' => now(),
        ]);

        $worker8 = User::create([
            'name' => 'Rowena Macaspac',
            'first_name' => 'Rowena',
            'middle_name' => 'Salazar',
            'last_name' => 'Macaspac',
            'email' => 'rowena@findjobph.com',
            'role' => 'worker',
            'sex' => 'Female',
            'password' => Hash::make('password'),
            'contact_number' => '09151239876',
            'address' => 'Tambler, General Santos City, South Cotabato',
            'email_verified_at' => now(),
        ]);

        $workerDev = User::create([
            'name' => 'Akil Cyrylle Dangel',
            'first_name' => 'Akil',
            'middle_name' => 'D',
            'last_name' => 'Dangel',
            'email' => 'akilcyrylle@gmail.com',
            'role' => 'worker',
            'sex' => 'Male',
            'password' => Hash::make('password'),
            'contact_number' => '09123456789',
            'address' => 'Ambuan, Catigbian, Bohol',
            'email_verified_at' => now(),
        ]);

        // ==========================================
        // 4. 20 JOBS: 6 LUZON, 7 VISAYAS, 7 MINDANAO
        // ==========================================

        // --- LUZON (6 JOBS) ---
        // 1. Metro Manila (BGC Taguig)
        $jobL1 = JobPost::create([
            'employer_id' => $empLuzon1->id,
            'title' => 'Structural Mason & Steel Rebar Fabricator',
            'category' => 'Construction & Skilled Trades',
            'job_type' => 'Full-time',
            'salary_range' => '₱750 - ₱950 / day',
            'workers_needed' => 12,
            'workers_hired' => 3,
            'location_address' => 'Fort Bonifacio, Taguig City, Metro Manila',
            'latitude' => 14.5547,
            'longitude' => 121.0509,
            'status' => 'open',
            'requirements' => "1. TESDA NC II Certificate in Masonry or Steelworks\n2. At least 1 year commercial high-rise construction experience\n3. Barangay Clearance, NBI Clearance, and Fit-to-Work Medical Certificate\n4. Strict adherence to safety hardhats and steel-toed boots",
            'description' => 'Megawide Capital Engineering is urgently hiring 12 skilled masons and rebar fabricators for our high-rise development in BGC. Free on-site barracks lodging, hazard pay, and daily meal allowances provided.',
        ]);

        // 2. Pampanga (San Fernando)
        $jobL2 = JobPost::create([
            'employer_id' => $empLuzon2->id,
            'title' => 'Grain Silo Operator & Feed Mill Crew',
            'category' => 'Agriculture & Farming',
            'job_type' => 'Full-time',
            'salary_range' => '₱620 - ₱750 / day',
            'workers_needed' => 8,
            'workers_hired' => 2,
            'location_address' => 'Dolores, San Fernando City, Pampanga',
            'latitude' => 15.0286,
            'longitude' => 120.6896,
            'status' => 'open',
            'requirements' => "1. High School graduate or vocational course\n2. Experience in agricultural feeds, grains, or warehouse bagging\n3. Fit to work and physically active\n4. Valid Gov ID & Police Clearance",
            'description' => 'Immediate openings for agricultural mill crew in San Fernando, Pampanga. Responsibilities include corn and rice grain loading, silo bagging, and feed machinery operation with weekly bonuses.',
        ]);

        // 3. Laguna (Calamba City)
        $jobL3 = JobPost::create([
            'employer_id' => $empLuzon1->id,
            'title' => 'Electronics Assembly Line Operator',
            'category' => 'Manufacturing & Factory',
            'job_type' => 'Full-time',
            'salary_range' => '₱16,000 - ₱21,000 / month',
            'workers_needed' => 15,
            'workers_hired' => 5,
            'location_address' => 'Parian, Calamba City, Laguna',
            'latitude' => 14.2117,
            'longitude' => 121.1656,
            'status' => 'open',
            'requirements' => "1. High School or Senior High School graduate\n2. Good hand dexterity and eye-to-hand coordination\n3. Willing to work on shifting schedules (Day/Night)\n4. SSS, PhilHealth, Pag-IBIG numbers ready",
            'description' => 'Cleanroom electronic components assembly line in Calamba Industrial Park. Air-conditioned facility, free shuttle bus service along Calamba and Santa Rosa, and complete health insurance coverage.',
        ]);

        // 4. Batangas (Lipa City)
        $jobL4 = JobPost::create([
            'employer_id' => $empLuzon2->id,
            'title' => 'Coffee Roasting Assistant & Barako Sorter',
            'category' => 'Agriculture & Farming',
            'job_type' => 'Daily Rate',
            'salary_range' => '₱580 - ₱700 / day',
            'workers_needed' => 6,
            'workers_hired' => 1,
            'location_address' => 'Sabang, Lipa City, Batangas',
            'latitude' => 13.9419,
            'longitude' => 121.1644,
            'status' => 'open',
            'requirements' => "1. Knowledge in Liberica (Barako) and Robusta bean harvesting and drying\n2. Attention to quality sorting and packaging\n3. Barangay Clearance and Health Certificate",
            'description' => 'Join our Batangas coffee estate in Lipa City! Daily sorting, dry milling, and packaging of authentic Kapeng Barako for export and specialty cafes. Free lunch and coffee allowance provided.',
        ]);

        // 5. Benguet (Baguio City)
        $jobL5 = JobPost::create([
            'employer_id' => $empLuzon3->id,
            'title' => 'Highland Resort Front Desk & Guest Coordinator',
            'category' => 'Hospitality & Tourism',
            'job_type' => 'Full-time',
            'salary_range' => '₱17,500 - ₱23,000 / month',
            'workers_needed' => 4,
            'workers_hired' => 1,
            'location_address' => 'Session Road, Baguio City, Benguet',
            'latitude' => 16.4023,
            'longitude' => 120.5960,
            'status' => 'open',
            'requirements' => "1. College degree or vocational graduate in Hospitality/Tourism\n2. Warm, polite communication skills in English & Tagalog\n3. Basic computer and booking system skills\n4. Resident of Baguio or nearby La Trinidad preferred",
            'description' => 'Welcoming guests to our boutique mountain retreat in Baguio City. Shifts include front desk registration, concierge assistance, and tour coordination with tips and 13th month pay.',
        ]);

        // 6. Pangasinan (Dagupan City)
        $jobL6 = JobPost::create([
            'employer_id' => $empLuzon3->id,
            'title' => 'Bangus Processing & Cold Storage Handler',
            'category' => 'Manufacturing & Factory',
            'job_type' => 'Contract',
            'salary_range' => '₱550 - ₱680 / day',
            'workers_needed' => 10,
            'workers_hired' => 2,
            'location_address' => 'Bonuan Gueset, Dagupan City, Pangasinan',
            'latitude' => 16.0433,
            'longitude' => 120.3333,
            'status' => 'open',
            'requirements' => "1. Experience in fish cleaning, deboning, and cryogenic cold storage handling\n2. Willing to work in cold temperature conditions (cold gear provided)\n3. Sanitary permit & Medical Certificate",
            'description' => 'Dagupan export-quality boneless milkfish (bangus) processing and vacuum packaging facility. Immediate hiring with overtime pay, free uniform, and cold storage safety gear.',
        ]);

        // --- VISAYAS (7 JOBS) ---
        // 7. Bohol (Catigbian)
        $jobV1 = JobPost::create([
            'employer_id' => $empVisayas1->id,
            'title' => 'Heavy Equipment Operator & Skilled Mason',
            'category' => 'Construction & Skilled Trades',
            'job_type' => 'Full-time',
            'salary_range' => '₱650 - ₱900 / day',
            'workers_needed' => 10,
            'workers_hired' => 2,
            'location_address' => 'Ambuan, Catigbian, Bohol, Philippines',
            'latitude' => 9.8433,
            'longitude' => 124.0287,
            'status' => 'open',
            'requirements' => "1. Valid Driver's License or TESDA NC II Heavy Equipment Certificate\n2. At least 1-2 years experience in site roadworks\n3. Barangay Clearance & Police Clearance\n4. Fit to work medical certificate",
            'description' => 'Urgent road concreting and drainage culvert project in Catigbian, Bohol. We provide free barracks lodging, protective safety boots, and daily food allowance for our municipal site crew.',
        ]);

        // 8. Bohol (Tagbilaran City)
        $jobV2 = JobPost::create([
            'employer_id' => $empVisayas1->id,
            'title' => 'Eco-Resort Landscaper & Maintenance Staff',
            'category' => 'Hospitality & Tourism',
            'job_type' => 'Contract',
            'salary_range' => '₱520 - ₱650 / day',
            'workers_needed' => 6,
            'workers_hired' => 2,
            'location_address' => 'Poblacion, Tagbilaran City, Bohol, Philippines',
            'latitude' => 9.6444,
            'longitude' => 123.8544,
            'status' => 'open',
            'requirements' => "1. Experience in tropical gardening, landscape turfing, and resort plumbing\n2. Trustworthy and diligent worker\n3. Barangay Clearance",
            'description' => 'Maintaining the lush tropical garden pathways, villas, and resort amenities in Tagbilaran, Bohol. Meals during shift included with monthly incentives.',
        ]);

        // 9. Cebu (Cebu City IT Park)
        $jobV3 = JobPost::create([
            'employer_id' => $empVisayas2->id,
            'title' => 'IT Technical Support & Helpdesk Specialist',
            'category' => 'IT & Tech Support',
            'job_type' => 'Full-time',
            'salary_range' => '₱22,000 - ₱30,000 / month',
            'workers_needed' => 5,
            'workers_hired' => 2,
            'location_address' => 'Lahug, Cebu City, Cebu, Philippines',
            'latitude' => 10.3297,
            'longitude' => 123.9064,
            'status' => 'open',
            'requirements' => "1. Graduate of Computer Science, IT, or Computer Engineering\n2. Knowledge in Windows/Linux network troubleshooting and ticketing\n3. Good command of spoken and written English\n4. Night differential pay eligible",
            'description' => 'Fast-growing tech firm in Cebu IT Park Lahug looking for dedicated helpdesk technicians. Enjoy HMO coverage from Day 1, hybrid work flexibility, and high-performance bonuses.',
        ]);

        // 10. Cebu (Mandaue City)
        $jobV4 = JobPost::create([
            'employer_id' => $empVisayas2->id,
            'title' => 'Logistics Warehouse Dispatcher & Forklift Driver',
            'category' => 'Logistics & Delivery',
            'job_type' => 'Full-time',
            'salary_range' => '₱650 - ₱800 / day',
            'workers_needed' => 8,
            'workers_hired' => 3,
            'location_address' => 'Subangdaku, Mandaue City, Cebu, Philippines',
            'latitude' => 10.3333,
            'longitude' => 123.9333,
            'status' => 'open',
            'requirements' => "1. TESDA NC II in Forklift Operation\n2. Experience in warehouse inventory dispatch and pallet loading\n3. NBI Clearance & Drug Test Result",
            'description' => 'Mandaue City primary distribution center for retail and consumer products. Immediate deployment for certified forklift operators and shipping clerks.',
        ]);

        // 11. Iloilo (Iloilo City)
        $jobV5 = JobPost::create([
            'employer_id' => $empVisayas3->id,
            'title' => 'Wholesale Grain & Fertilizer Cargo Handler',
            'category' => 'General Labor',
            'job_type' => 'Daily Rate',
            'salary_range' => '₱540 - ₱680 / day',
            'workers_needed' => 12,
            'workers_hired' => 4,
            'location_address' => 'Mandurriao, Iloilo City, Iloilo, Philippines',
            'latitude' => 10.7202,
            'longitude' => 122.5621,
            'status' => 'open',
            'requirements' => "1. Physically strong and capable of heavy cargo handling\n2. Punctual, honest, and reliable team player\n3. Barangay Clearance",
            'description' => 'Cargo handling, grain unloading, and warehouse stack organization at the Mandurriao wholesale agro-trading depot in Iloilo City.',
        ]);

        // 12. Negros Occidental (Bacolod City)
        $jobV6 = JobPost::create([
            'employer_id' => $empVisayas3->id,
            'title' => 'Sugarcane Farm Tractor Operator & Field Mechanic',
            'category' => 'Agriculture & Farming',
            'job_type' => 'Contract',
            'salary_range' => '₱600 - ₱780 / day',
            'workers_needed' => 8,
            'workers_hired' => 2,
            'location_address' => 'Mansilingan, Bacolod City, Negros Occidental, Philippines',
            'latitude' => 10.6765,
            'longitude' => 122.9509,
            'status' => 'open',
            'requirements' => "1. Professional driver's license (Restrictions 1, 2, 3 or heavy machinery)\n2. Familiarity with farm tractors and sugarcane harvesting attachments\n3. Medical certificate",
            'description' => 'Mechanized sugarcane planting and land preparation in Bacolod City. Modern air-conditioned tractors, complete safety gear, and harvest completion bonus.',
        ]);

        // 13. Leyte (Tacloban City)
        $jobV7 = JobPost::create([
            'employer_id' => $empVisayas1->id,
            'title' => 'Bridge Concreting & Concrete Pump Operator',
            'category' => 'Construction & Skilled Trades',
            'job_type' => 'Project-based',
            'salary_range' => '₱700 - ₱900 / day',
            'workers_needed' => 10,
            'workers_hired' => 1,
            'location_address' => 'Sagkahan, Tacloban City, Leyte, Philippines',
            'latitude' => 11.2433,
            'longitude' => 125.0039,
            'status' => 'open',
            'requirements' => "1. Experience in coastal highway and bridge foundation works\n2. Concrete boom pump or mixer truck handling skills\n3. Valid ID and police clearance",
            'description' => 'Coastal road and bypass bridge infrastructure project in Tacloban City. Complete site insurance, meal allowances, and overtime compensation.',
        ]);

        // --- MINDANAO (7 JOBS) ---
        // 14. Davao del Sur (Davao City)
        $jobM1 = JobPost::create([
            'employer_id' => $empMindanao1->id,
            'title' => 'Banana & Durian Packing Plant Quality Inspector',
            'category' => 'Agriculture & Farming',
            'job_type' => 'Full-time',
            'salary_range' => '₱18,000 - ₱24,000 / month',
            'workers_needed' => 6,
            'workers_hired' => 2,
            'location_address' => 'Buhangin, Davao City, Davao del Sur, Philippines',
            'latitude' => 7.0731,
            'longitude' => 125.6128,
            'status' => 'open',
            'requirements' => "1. College or vocational graduate in Agriculture, Food Tech, or related field\n2. Experience in fresh export fruit grading, sizing, and phytosanitary standards\n3. High attention to detail",
            'description' => 'Direct inspection and packaging of export-grade Cavendish bananas and fresh durian in Buhangin, Davao City. Air-conditioned packing facility with annual bonuses.',
        ]);

        // 15. Misamis Oriental (Cagayan de Oro City)
        $jobM2 = JobPost::create([
            'employer_id' => $empMindanao3->id,
            'title' => 'Commercial Harbor Welder & Metal Fabricator',
            'category' => 'Construction & Skilled Trades',
            'job_type' => 'Full-time',
            'salary_range' => '₱700 - ₱920 / day',
            'workers_needed' => 10,
            'workers_hired' => 3,
            'location_address' => 'Lapasan, Cagayan de Oro City, Misamis Oriental, Philippines',
            'latitude' => 8.4822,
            'longitude' => 124.6472,
            'status' => 'open',
            'requirements' => "1. TESDA NC II in SMAW (Shielded Metal Arc Welding) or FCAW\n2. Minimum 1 year welding experience on industrial structural steel\n3. Complete PPE gear and clearance",
            'description' => 'Port expansion and metal silo construction project at the CDO commercial harbor in Lapasan. Free safety equipment, hot meals during overtime, and weekly payroll.',
        ]);

        // 16. South Cotabato (General Santos City)
        $jobM3 = JobPost::create([
            'employer_id' => $empMindanao2->id,
            'title' => 'Deep-Sea Tuna Processing & Cannery Line Technician',
            'category' => 'Manufacturing & Factory',
            'job_type' => 'Full-time',
            'salary_range' => '₱17,000 - ₱22,000 / month',
            'workers_needed' => 15,
            'workers_hired' => 4,
            'location_address' => 'Tambler, General Santos City, South Cotabato, Philippines',
            'latitude' => 6.1164,
            'longitude' => 125.1716,
            'status' => 'open',
            'requirements' => "1. High School graduate or vocational electronics/mechanical training\n2. Willingness to work in tuna cannery and steam retort lines\n3. Updated health certificate and sanitary permit",
            'description' => 'GenSan Tuna Capital of the Philippines! We are hiring line technicians for our premier export canning facility in Tambler. Free company shuttle, meal subsidy, and health benefits.',
        ]);

        // 17. Zamboanga del Sur (Zamboanga City)
        $jobM4 = JobPost::create([
            'employer_id' => $empMindanao2->id,
            'title' => 'Marine Cold Storage Inventory Clerk & Forklift Crew',
            'category' => 'Logistics & Delivery',
            'job_type' => 'Full-time',
            'salary_range' => '₱580 - ₱720 / day',
            'workers_needed' => 8,
            'workers_hired' => 2,
            'location_address' => 'Canelar, Zamboanga City, Zamboanga del Sur, Philippines',
            'latitude' => 6.9214,
            'longitude' => 122.0790,
            'status' => 'open',
            'requirements' => "1. Experience in cold storage seafood warehousing\n2. Basic knowledge in barcoding and shipping manifests\n3. Fit to work in low temperatures",
            'description' => 'Inventory coordination, container unboxing, and freezing storage logistics for international seafood cargo in Canelar, Zamboanga City.',
        ]);

        // 18. Bukidnon (Malaybalay City)
        $jobM5 = JobPost::create([
            'employer_id' => $empMindanao1->id,
            'title' => 'Pineapple Plantation Agronomist & Field Supervisor',
            'category' => 'Agriculture & Farming',
            'job_type' => 'Contract',
            'salary_range' => '₱20,000 - ₱26,000 / month',
            'workers_needed' => 5,
            'workers_hired' => 1,
            'location_address' => 'Casisang, Malaybalay City, Bukidnon, Philippines',
            'latitude' => 8.1575,
            'longitude' => 125.1278,
            'status' => 'open',
            'requirements' => "1. BS Agriculture / Agronomy / Horticulture degree\n2. Motorcycle driving license for field monitoring\n3. Experience in fertilization, pest control, and soil testing",
            'description' => 'Highland pineapple cultivation and plantation monitoring in scenic Casisang, Malaybalay City. Motorized allowance, housing subsidy, and productivity bonuses.',
        ]);

        // 19. Agusan del Norte (Butuan City)
        $jobM6 = JobPost::create([
            'employer_id' => $empMindanao3->id,
            'title' => 'Heavy Hauler Truck Driver & Highway Crew',
            'category' => 'Logistics & Delivery',
            'job_type' => 'Full-time',
            'salary_range' => '₱750 - ₱950 / day',
            'workers_needed' => 8,
            'workers_hired' => 2,
            'location_address' => 'Doongan, Butuan City, Agusan del Norte, Philippines',
            'latitude' => 8.9492,
            'longitude' => 125.5436,
            'status' => 'open',
            'requirements' => "1. Professional Driver's License Code 3 / 8 (Heavy vehicles & 10-wheeler trucks)\n2. Clean driving record with at least 2 years long-haul cargo experience\n3. NBI and Drug test clearance",
            'description' => 'Long-haul transport of timber, machinery, and construction materials across northern Mindanao routes. Generous trip allowances and complete mechanical support team.',
        ]);

        // 20. Maguindanao / Cotabato City
        $jobM7 = JobPost::create([
            'employer_id' => $empMindanao1->id,
            'title' => 'Rice & Corn Milling Technician',
            'category' => 'General Labor',
            'job_type' => 'Daily Rate',
            'salary_range' => '₱550 - ₱680 / day',
            'workers_needed' => 6,
            'workers_hired' => 1,
            'location_address' => 'Rosary Heights, Cotabato City, Maguindanao, Philippines',
            'latitude' => 7.2236,
            'longitude' => 124.2464,
            'status' => 'open',
            'requirements' => "1. Familiar with mechanical grain huskers, separators, and bagging equipment\n2. Punctual, honest, and physically fit\n3. Barangay Clearance",
            'description' => 'Grain processing plant in Cotabato City handling central Mindanao rice harvest. Daily rate with overtime pay, safety equipment, and rice allowance after 3 months.',
        ]);

        // ==========================================
        // 5. APPLICATIONS & ACTIVITY
        // ==========================================
        JobApplication::create([
            'job_post_id' => $jobV1->id,
            'worker_id' => $worker1->id,
            'status' => 'hired',
            'hired_at' => now()->subDays(3),
            'cover_letter' => 'Good day Sir! I am a Catigbian resident with TESDA NC II in Heavy Equipment. I am ready to report for the Ambuan road project.',
            'submitted_requirements' => 'TESDA NC II Heavy Equipment, Barangay Clearance, Driver License',
        ]);

        JobApplication::create([
            'job_post_id' => $jobV2->id,
            'worker_id' => $worker2->id,
            'status' => 'hired',
            'hired_at' => now()->subDays(5),
            'cover_letter' => 'Applying for eco-resort groundskeeper. Experienced in tropical plants and landscaping in Tagbilaran.',
            'submitted_requirements' => 'High School Diploma, Barangay Clearance',
        ]);

        JobApplication::create([
            'job_post_id' => $jobV4->id,
            'worker_id' => $worker3->id,
            'status' => 'hired',
            'hired_at' => now()->subDays(6),
            'cover_letter' => 'Experienced forklift driver living in Mandaue City. Valid TESDA NC II certificate.',
            'submitted_requirements' => 'TESDA NC II Forklift, NBI Clearance, Medical Cert',
        ]);

        JobApplication::create([
            'job_post_id' => $jobL2->id,
            'worker_id' => $worker4->id,
            'status' => 'hired',
            'hired_at' => now()->subDays(2),
            'cover_letter' => 'Pampanga resident with grain warehouse background applying for feed mill crew.',
            'submitted_requirements' => 'High School Diploma, Police Clearance',
        ]);

        JobApplication::create([
            'job_post_id' => $jobL3->id,
            'worker_id' => $worker5->id,
            'status' => 'hired',
            'hired_at' => now()->subDays(4),
            'cover_letter' => 'Calamba resident applying for electronics assembly line operator.',
            'submitted_requirements' => 'Senior High School Diploma, Medical Fit to Work',
        ]);

        JobApplication::create([
            'job_post_id' => $jobL5->id,
            'worker_id' => $worker6->id,
            'status' => 'hired',
            'hired_at' => now()->subDays(3),
            'cover_letter' => 'Baguio City native with hospitality background applying for front desk coordinator.',
            'submitted_requirements' => 'BS Tourism Transcript, NBI Clearance',
        ]);

        JobApplication::create([
            'job_post_id' => $jobM1->id,
            'worker_id' => $worker7->id,
            'status' => 'hired',
            'hired_at' => now()->subDays(2),
            'cover_letter' => 'Applying as fruit inspector in Buhangin, Davao City. Experienced in fruit grading.',
            'submitted_requirements' => 'BS Agriculture Diploma, Barangay Clearance',
        ]);

        JobApplication::create([
            'job_post_id' => $jobM3->id,
            'worker_id' => $worker8->id,
            'status' => 'hired',
            'hired_at' => now()->subDay(),
            'cover_letter' => 'Applying for canning line technician in Tambler, GenSan.',
            'submitted_requirements' => 'Health Card, Sanitary Permit, Vocational Certificate',
        ]);

        // ==========================================
        // 6. DIRECT MESSAGES
        // ==========================================
        Message::create([
            'sender_id' => $worker1->id,
            'receiver_id' => $empVisayas1->id,
            'job_post_id' => $jobV1->id,
            'message' => 'Good day Sir Roberto! I submitted my TESDA certificate and Barangay Clearance for the Heavy Equipment Operator post in Ambuan, Catigbian.',
            'is_read' => true,
            'created_at' => now()->subDays(3)->subHours(4),
        ]);

        Message::create([
            'sender_id' => $empVisayas1->id,
            'receiver_id' => $worker1->id,
            'job_post_id' => $jobV1->id,
            'message' => 'Hello Juan! We reviewed your credentials. You have been HIRED as part of our Catigbian road concreting team. Please report to the site office this Monday at 7:30 AM.',
            'is_read' => true,
            'created_at' => now()->subDays(3)->subHours(2),
        ]);

        Message::create([
            'sender_id' => $worker1->id,
            'receiver_id' => $empVisayas1->id,
            'job_post_id' => $jobV1->id,
            'message' => 'Salamat kaayo Sir Roberto! I will be at the site early with my safety boots and hard hat ready.',
            'is_read' => false,
            'created_at' => now()->subHours(8),
        ]);
    }
}
