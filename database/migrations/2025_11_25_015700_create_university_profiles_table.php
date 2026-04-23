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
        Schema::create('university_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('profile_id')->constrained('profiles')->onDelete('cascade');
    
            $table->string('university_name')->nullable();
            $table->string('degree_program')->nullable();  // BS CS, BBA etc
            $table->string('current_semester')->nullable();
            $table->decimal('cgpa', 3, 2)->nullable();
            $table->text('interests')->nullable();
    
            $table->timestamps();
        });
    }
    
    public function down(): void
    {
        Schema::dropIfExists('university_profiles');
    }
    
};
