<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('career_trend_snapshots', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('career_trend_id')->nullable();
            $table->string('career_name');
            $table->string('region')->default('Pakistan');

            $table->integer('trend_score')->nullable();
            $table->integer('job_openings')->nullable();
            $table->integer('search_volume')->nullable();

            $table->date('snapshot_date'); // yahan se hum 6 months ka history dekhen gay
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('career_trend_snapshots');
    }
};

