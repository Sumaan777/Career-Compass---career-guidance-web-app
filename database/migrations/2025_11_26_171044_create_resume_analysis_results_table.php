<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('resume_analysis_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('resume_id')->constrained()->onDelete('cascade');  
            // when resume deleted → auto delete analysis
    
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
    
            $table->longText('summary')->nullable();
            $table->longText('strengths')->nullable();
            $table->longText('weaknesses')->nullable();
            $table->longText('missing_skills')->nullable();
            $table->longText('suggested_roles')->nullable();
            $table->integer('score')->nullable();
    
            $table->timestamps();
        });
    }
    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resume_analysis_results');
    }
};
