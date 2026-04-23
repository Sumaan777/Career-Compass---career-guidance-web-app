<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Questionnaire;
use App\Models\Question;
use App\Models\Option;

class LogicQuestionnaireSeeder extends Seeder
{
    public function run(): void
    {
        // 🔹 Create questionnaire
        $questionnaire = Questionnaire::create([
            'title'       => 'Logic-Based Onboarding',
            'description' => 'Quick onboarding questions to determine your user type.',
            'user_type'   => 'logic_onboarding',
        ]);

        // 🔹 Add questions (simple and clear for user classification)
        $questions = [
            [
                'question_text' => '🎓 Which best describes your current status?',
                'question_type' => 'select',
                'options' => [
                    'High School Student',
                    'University Student',
                    'Fresh Graduate',
                    'Working Professional (Career Switcher)',
                    'Undecided / Exploring',
                ],
            ],
            [
                'question_text' => '💭 What are you currently focusing on the most?',
                'question_type' => 'select',
                'options' => [
                    'Completing school or exams',
                    'Studying for a degree',
                    'Looking for my first job',
                    'Trying to change careers',
                    'Still exploring my options',
                ],
            ],
            [
                'question_text' => '🚀 What do you hope to gain from CareerCompass?',
                'question_type' => 'select',
                'options' => [
                    'Guidance for choosing the right field',
                    'University degree planning help',
                    'Career advice and job direction',
                    'Skill-building and upskilling suggestions',
                    'Just exploring for now',
                ],
            ],
        ];

        // 🔹 Insert questions and options
        foreach ($questions as $q) {
            $question = Question::create([
                'questionnaire_id' => $questionnaire->id,
                'question_text'    => $q['question_text'],
                'question_type'    => $q['question_type'],
            ]);

            foreach ($q['options'] as $opt) {
                Option::create([
                    'question_id' => $question->id,
                    'option_text' => $opt,
                ]);
            }
        }
    }
}
