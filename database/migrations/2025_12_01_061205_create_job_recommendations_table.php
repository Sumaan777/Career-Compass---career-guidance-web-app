<?php

// database/migrations/2025_11_30_000000_create_job_recommendations_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('job_recommendations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');

            // Snapshot of user when search was made
            $table->string('degree')->nullable();
            $table->string('skills')->nullable();
            $table->string('field_of_interest')->nullable();
            $table->string('experience_years')->nullable();
            $table->string('location')->nullable(); // user-selected

            // AI suggested job cluster
            $table->string('ai_job_title');      // e.g. "Junior Laravel Developer"
            $table->string('reason')->nullable(); // why AI suggested it

            // External job listing fields
            $table->string('source')->nullable();          // e.g. "adzuna"
            $table->string('job_title')->nullable();
            $table->string('company')->nullable();
            $table->string('job_location')->nullable();
            $table->string('redirect_url')->nullable();
            $table->string('salary')->nullable();          // simple text (you can normalize later)
            $table->timestamp('posted_at')->nullable();

            // Matching score (0–100) you compute later if you want
            $table->unsignedTinyInteger('match_score')->nullable();

            $table->json('raw_api')->nullable(); // optional raw payload
            $table->timestamps();

            $table->foreign('user_id')
                ->references('id')->on('users')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_recommendations');
    }
};

