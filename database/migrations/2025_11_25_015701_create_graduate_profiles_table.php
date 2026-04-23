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
        Schema::create('graduate_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('profile_id')->constrained('profiles')->onDelete('cascade');
    
            $table->string('university_name')->nullable();
            $table->string('degree_name')->nullable();   // BS, MS
            $table->string('major')->nullable();         // CS, EE, etc
            $table->year('graduation_year')->nullable();
            $table->decimal('cgpa', 3, 2)->nullable();
            $table->string('final_project_title')->nullable();
    
            $table->timestamps();
        });
    }
    
    public function down(): void
    {
        Schema::dropIfExists('graduate_profiles');
    }
    
};
