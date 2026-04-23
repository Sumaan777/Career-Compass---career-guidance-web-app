<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('responses', function (Blueprint $table) {
            // ✅ Only change column nullability, no new foreign keys
            $table->unsignedBigInteger('questionnaire_id')->nullable()->change();
            $table->unsignedBigInteger('question_id')->nullable()->change();
            $table->unsignedBigInteger('option_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('responses', function (Blueprint $table) {
            // Revert columns to NOT NULL (if needed)
            $table->unsignedBigInteger('questionnaire_id')->nullable(false)->change();
            $table->unsignedBigInteger('question_id')->nullable(false)->change();
            $table->unsignedBigInteger('option_id')->nullable(false)->change();
        });
    }
};
