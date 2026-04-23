<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('skill_gap_analyses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Target career for this analysis
            $table->string('target_career')->nullable();

            // JSON arrays
            $table->json('current_skills')->nullable();
            $table->json('required_skills')->nullable();
            $table->json('missing_skills')->nullable();
            $table->json('matched_skills')->nullable();
            $table->json('extra_skills')->nullable();

            // Raw AI response if you want to debug later
            $table->json('raw_ai_response')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('skill_gap_analyses');
    }
};
