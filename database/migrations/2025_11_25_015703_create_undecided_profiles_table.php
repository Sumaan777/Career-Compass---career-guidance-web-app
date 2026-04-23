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
        Schema::create('undecided_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('profile_id')->constrained('profiles')->onDelete('cascade');
    
            $table->text('interests')->nullable();
            $table->text('strengths')->nullable();
            $table->string('motivation_level')->nullable();      // low/medium/high or 1–5
            $table->string('preferred_learning_style')->nullable(); // visual, hands-on, etc
    
            $table->timestamps();
        });
    }
    
    public function down(): void
    {
        Schema::dropIfExists('undecided_profiles');
    }
    
};
