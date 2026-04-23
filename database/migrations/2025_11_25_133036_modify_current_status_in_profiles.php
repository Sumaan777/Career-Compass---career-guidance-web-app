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
        Schema::table('profiles', function (Blueprint $table) {
            // Convert ENUM → VARCHAR
            $table->string('current_status')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('profiles', function (Blueprint $table) {
            // If you want to revert back to ENUM (optional)
            $table->enum('current_status', [
                'undecided',
                'student',
                'graduate',
                'career_switcher',
                'fresh_graduate'
            ])->nullable()->change();
        });
    }
};
