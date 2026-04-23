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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->string('email', 255)->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->enum('user_type', [
                'undecided',
                'high_school',
                'graduating_student',
                'fresh_graduate',
                'career_switcher'
            ])->default('undecided')->index();
            $table->string('profile_photo', 500)->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->boolean('profile_completed')->default(false);
            
            // Add indexes for better performance
            $table->index('email');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
