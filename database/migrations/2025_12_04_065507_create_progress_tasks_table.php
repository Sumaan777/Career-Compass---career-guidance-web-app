<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('progress_tasks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('progress_id'); // FK to career_progresses.id

            $table->string('task_title');              // e.g. "Learn HTML basics"
            $table->text('task_description')->nullable();
            $table->string('phase_name')->nullable();  // e.g. "Foundation", "Portfolio"
            $table->boolean('is_completed')->default(false);
            $table->unsignedInteger('order_number')->default(0);

            $table->timestamps();

            // (Optional) foreign key
            // $table->foreign('progress_id')->references('id')->on('career_progresses')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('progress_tasks');
    }
};
