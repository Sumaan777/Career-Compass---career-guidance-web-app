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
        Schema::create('switcher_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('profile_id')->constrained('profiles')->onDelete('cascade');
    
            $table->string('current_field')->nullable();     // e.g. Mechanical Eng
            $table->string('previous_field')->nullable();    // e.g. Civil Eng
            $table->integer('past_experience_years')->nullable();
    
            $table->json('skills_json')->nullable();         // ["Java","Leadership"]
            $table->json('certifications_json')->nullable(); // ["AWS","PMP"]
            $table->json('past_roles_json')->nullable();     // ["Junior Dev","PM"]
    
            $table->timestamps();
        });
    }
    
    public function down(): void
    {
        Schema::dropIfExists('switcher_profiles');
    }
    
};
