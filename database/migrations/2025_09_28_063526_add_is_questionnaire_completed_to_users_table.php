<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'is_questionnaire_completed')) {
                $table->boolean('is_questionnaire_completed')->default(false)->after('profile_photo');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'is_questionnaire_completed')) {
                $table->dropColumn('is_questionnaire_completed');
            }
        });
    }
};
