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
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('full_name')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->enum('education_level', ['HighSchool', 'University', 'Graduate', 'Professional'])->nullable();
            $table->enum('current_status', ['undecided', 'student', 'fresh_graduate', 'career_switcher'])->default('undecided');
            $table->string('field_of_interest')->nullable();
            $table->json('skills')->nullable(); // store as JSON array
            $table->integer('experience_years')->nullable();
            $table->string('resume_path')->nullable();
            $table->string('profile_photo')->nullable();
            $table->text('bio')->nullable();
            $table->string('location')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};
