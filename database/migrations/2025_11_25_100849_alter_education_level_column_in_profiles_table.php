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
    Schema::table('profiles', function (Blueprint $table) {
        $table->string('education_level', 255)->nullable()->change();
    });
}

public function down()
{
    Schema::table('profiles', function (Blueprint $table) {
        $table->string('education_level', 50)->change();
    });
}

};
