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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // Link to users table
            $table->string('title'); // e.g., "AI Career Guidance System"
            $table->text('description')->nullable(); // Details about the project
            $table->string('role')->nullable(); // e.g., "Team Lead", "Developer"
            $table->string('technologies')->nullable(); // e.g., "Laravel, Python, MySQL"
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('project_url')->nullable(); // GitHub or live project link
            $table->timestamps();

            // Foreign key
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
