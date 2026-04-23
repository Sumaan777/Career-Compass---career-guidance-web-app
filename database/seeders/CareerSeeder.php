<?php

// database/seeders/CareerSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Career;
use Illuminate\Support\Str;

class CareerSeeder extends Seeder
{
    public function run(): void
    {
        $careers = [
            ['title' => 'AI Engineer', 'category' => 'Technology'],
            ['title' => 'Machine Learning Engineer', 'category' => 'Technology'],
            ['title' => 'Data Scientist', 'category' => 'Technology'],
            ['title' => 'Frontend Developer', 'category' => 'Technology'],
            ['title' => 'Backend Developer', 'category' => 'Technology'],
            ['title' => 'Full Stack Developer', 'category' => 'Technology'],
            ['title' => 'Cybersecurity Analyst', 'category' => 'Technology'],
            ['title' => 'Cloud Architect', 'category' => 'Technology'],
            ['title' => 'UI/UX Designer', 'category' => 'Design'],
            ['title' => 'Graphic Designer', 'category' => 'Design'],
            ['title' => 'Digital Marketer', 'category' => 'Marketing'],
            ['title' => 'SEO Specialist', 'category' => 'Marketing'],
            ['title' => 'Content Writer', 'category' => 'Writing'],
            ['title' => 'Business Analyst', 'category' => 'Business'],
            ['title' => 'Project Manager', 'category' => 'Business'],
            ['title' => 'Doctor', 'category' => 'Healthcare'],
            ['title' => 'Nurse', 'category' => 'Healthcare'],
        ];

        foreach ($careers as $c) {
            Career::updateOrCreate(
                ['slug' => Str::slug($c['title'])],
                [
                    'title' => $c['title'],
                    'category' => $c['category'] ?? null,
                ]
            );
        }
    }
}

