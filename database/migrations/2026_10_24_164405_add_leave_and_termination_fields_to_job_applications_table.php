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
        Schema::table('job_applications', function (Blueprint $table) {
            $table->text('leave_reason')->nullable()->after('attachment_path');
            $table->timestamp('leave_requested_at')->nullable()->after('leave_reason');
            $table->text('termination_reason')->nullable()->after('leave_requested_at');
            $table->timestamp('ended_at')->nullable()->after('termination_reason');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_applications', function (Blueprint $table) {
            $table->dropColumn([
                'leave_reason',
                'leave_requested_at',
                'termination_reason',
                'ended_at',
            ]);
        });
    }
};
