<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('academic_path_results', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');

            $table->string('target_career')->nullable();
            $table->json('education_match')->nullable(); // matched, missing, optional
            $table->json('required_degrees')->nullable(); 
            $table->json('recommended_paths')->nullable();
            $table->json('certifications')->nullable();
            $table->text('summary')->nullable();

            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void {
        Schema::dropIfExists('academic_path_results');
    }
};
