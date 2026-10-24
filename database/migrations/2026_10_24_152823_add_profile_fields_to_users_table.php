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
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('worker')->after('email'); // 'admin', 'worker', 'employer'
            $table->string('first_name')->nullable()->after('name');
            $table->string('last_name')->nullable()->after('first_name');
            $table->string('sex')->nullable()->after('last_name'); // 'Male', 'Female', 'Other'
            $table->text('address')->nullable()->after('sex');
            $table->string('contact_number')->nullable()->after('address');
            $table->string('company_name')->nullable()->after('contact_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'role',
                'first_name',
                'last_name',
                'sex',
                'address',
                'contact_number',
                'company_name',
            ]);
        });
    }
};
