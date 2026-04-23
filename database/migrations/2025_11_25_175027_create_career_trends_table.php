<?php
// database/migrations/2025_11_25_000000_create_career_trends_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('career_trends', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('career_id')->nullable(); // link to careers table (optional)
            $table->string('career_name');
            $table->string('region')->default('Pakistan');

            $table->string('demand_level')->nullable();       // High / Medium / Low
            $table->integer('trend_score')->nullable();       // 0–100
            $table->string('trend_direction')->nullable();    // Rising / Stable / Falling

            $table->integer('job_openings')->nullable();      // Approx count
            $table->integer('search_volume')->nullable();     // Google-style searches estimation

            $table->json('top_skills')->nullable();
            $table->json('top_roles')->nullable();

            $table->text('insight_summary')->nullable();      // 2–3 lines
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('career_trends');
    }
};
