<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_quizzes', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('career_quiz_id')->nullable(); // reference to career_quizzes

            $table->text('question_text');
            $table->integer('question_order')->nullable();

            $table->timestamps();

            // Foreign Keys
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('career_quiz_id')->references('id')->on('career_quizzes')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_quizzes');
    }
};
