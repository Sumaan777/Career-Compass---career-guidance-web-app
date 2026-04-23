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
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            
            // Every question belongs to a questionnaire (e.g., Career Discovery)
            $table->unsignedBigInteger('questionnaire_id')->default(1);

            // The question text (supports emojis and long sentences)
            $table->text('question_text');

            // Use string instead of enum to avoid truncation issues
            // ('text' for open-ended, 'select' for multiple-choice)
            $table->string('question_type', 20)->default('text');

            // Optional: if you want to categorize questions later (e.g., student, graduate)
            $table->string('category')->nullable();

            $table->timestamps();

            // Optional foreign key if you have a 'questionnaires' table
            // $table->foreign('questionnaire_id')->references('id')->on('questionnaires')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
