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
        Schema::create('company_jobs', function (Blueprint $table) {
            // $table->id();
            $table->string('job_id')->primary();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('job_title');
            $table->longText('job_description')->nullable();
            $table->string('job_category_id');
            $table->string('job_location')->nullable();
            $table->string('job_city')->nullable();
            $table->string('job_state')->nullable();
            $table->string('job_country')->nullable();
            $table->string('job_zip_code')->nullable();
            $table->string('job_status')->default('active');
            $table->string('job_salary')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_jobs');
    }
};
