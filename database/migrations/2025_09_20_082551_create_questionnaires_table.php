<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('questionnaires', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // e.g., Career Discovery Quiz
            $table->text('description')->nullable();
            $table->enum('target_user', [
                'undecided',
                'high_school',
                'graduating_student',
                'fresh_graduate',
                'career_switcher',
                'all'
            ])->default('all'); // which user type it's for
            $table->unsignedBigInteger('user_id');

            // Foreign key constraint (optional but recommended)
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('questionnaires');
    }
};
