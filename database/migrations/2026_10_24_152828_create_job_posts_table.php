<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('job_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employer_id')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->string('category')->default('General Labor');
            $table->string('job_type')->default('Full-time');
            $table->string('salary_range')->nullable();
            $table->text('description');
            $table->text('requirements');
            $table->unsignedInteger('workers_needed')->default(1);
            $table->unsignedInteger('workers_hired')->default(0);
            $table->string('location_address');
            $table->decimal('latitude', 10, 7)->default(12.8797);
            $table->decimal('longitude', 10, 7)->default(121.7740);
            $table->string('status')->default('open'); // 'open', 'locked', 'archived'
            $table->timestamp('archived_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'created_at']);
            $table->index(['employer_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_posts');
    }
};
