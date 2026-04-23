<?php

// database/migrations/2025_11_25_000000_create_careers_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('careers', function (Blueprint $table) {
            $table->id();
            $table->string('title');              // e.g. "AI Engineer"
            $table->string('slug')->unique();     // "ai-engineer"
            $table->string('category')->nullable(); // e.g. "Technology"
            $table->text('short_description')->nullable();
            $table->json('skills_tags')->nullable(); // ["python","ml","neural networks"]
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('careers');
    }
};
