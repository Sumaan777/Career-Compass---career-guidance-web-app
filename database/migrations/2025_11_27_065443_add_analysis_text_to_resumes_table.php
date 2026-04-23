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
        Schema::table('resumes', function (Blueprint $table) {
            $table->longText('analysis_text')->nullable()->after('original_name');
        });
    }
    
    public function down()
    {
        Schema::table('resumes', function (Blueprint $table) {
            $table->dropColumn('analysis_text');
        });
    }
    

    
};
