<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('user_type', 50)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // revert back to enum if needed
            $table->enum('user_type', [
                'highschool',
                'graduate',
                'fresh',
                'switcher',
                'undecided'
            ])->nullable()->change();
        });
    }
};
