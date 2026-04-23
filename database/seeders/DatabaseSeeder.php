<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\CareerQuiz;
use App\Models\CareerQuestion;
use App\Models\CareerAnswer;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $this->call([
            CareerQuizSeeder::class,
            CareerQuestionsSeeder::class,
            CareerAnswersSeeder::class,
        ]);
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->call([
            CareerSeeder::class,
        ]); 
    }
}
