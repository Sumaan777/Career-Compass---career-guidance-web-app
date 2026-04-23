<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("
            UPDATE profiles
            JOIN users ON users.id = profiles.user_id
            SET profiles.full_name = users.name
            WHERE profiles.full_name IS NULL
               OR profiles.full_name = ''
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // ❗ We do NOT revert this because it is a data-fix migration
    }
};
