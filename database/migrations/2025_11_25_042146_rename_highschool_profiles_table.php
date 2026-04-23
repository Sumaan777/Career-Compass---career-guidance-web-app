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
        Schema::rename('highschool_profiles', 'high_school_profiles');
    }
    
    public function down()
    {
        Schema::rename('high_school_profiles', 'highschool_profiles');
    }
    
};
