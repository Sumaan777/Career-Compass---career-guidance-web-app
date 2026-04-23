<?php
// database/migrations/2025_11_30_000001_create_job_searches_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('job_searches', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');

            // snapshot
            $table->string('degree')->nullable();
            $table->string('skills')->nullable();
            $table->string('field_of_interest')->nullable();
            $table->string('experience_years')->nullable();
            $table->string('location')->nullable();

            $table->unsignedInteger('total_results')->default(0);
            $table->json('ai_jobs')->nullable(); // list of AI titles suggested
            $table->timestamps();

            $table->foreign('user_id')
                ->references('id')->on('users')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_searches');
    }
};

