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
        Schema::create('job_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_post_id')->constrained('job_posts')->cascadeOnDelete();
            $table->foreignId('worker_id')->constrained('users')->cascadeOnDelete();
            $table->string('status')->default('pending'); // 'pending', 'hired', 'rejected'
            $table->text('cover_letter')->nullable();
            $table->text('submitted_requirements')->nullable();
            $table->string('attachment_path')->nullable();
            $table->timestamp('hired_at')->nullable();
            $table->timestamps();

            $table->unique(['job_post_id', 'worker_id']);
            $table->index(['job_post_id', 'status']);
            $table->index(['worker_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_applications');
    }
};
