<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('learning_resources', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');

            $table->string('skill');             // e.g., "Laravel"
            $table->string('title');             // Course title
            $table->string('platform')->nullable(); // Udemy / Coursera / YouTube
            $table->string('url')->nullable();
            $table->text('description')->nullable();
            $table->string('difficulty')->nullable(); // Beginner / Intermediate
            $table->integer('duration')->nullable();  // Hours
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('learning_resources');
    }
};
