<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Drop constraint safely if it exists
        try {
            DB::statement('ALTER TABLE profiles DROP CHECK `profiles.skills`');
        } catch (\Exception $e) {
            // ignore if not exists
        }

        try {
            DB::statement('ALTER TABLE profiles DROP CONSTRAINT `profiles_skills_check`');
        } catch (\Exception $e) {
            // ignore if not exists
        }

        // Modify column type to TEXT (if not already)
        Schema::table('profiles', function (Blueprint $table) {
            $table->text('skills')->nullable()->change();
        });
    }

    public function down(): void
    {
        // Optional rollback: make it varchar(255) again
        Schema::table('profiles', function (Blueprint $table) {
            $table->string('skills', 255)->nullable()->change();
        });
    }
};
