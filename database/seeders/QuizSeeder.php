<?php

namespace Database\Seeders;

use App\Models\Quiz;
use App\Models\QuizQuestion;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class QuizSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Quiz 1: KALIKASAN AT ISTRUKTURA NG WIKANG FILIPINO
        $quiz1 = Quiz::create([
            'title' => 'Kalikasan at Istruktura ng Wikang Filipino',
            'slug' => Str::slug('Kalikasan at Istruktura ng Wikang Filipino'),
            'description' => 'Pagsusulit tungkol sa salitang-ugat, panlapi, at pagbabagong morpoponemiko.',
            'time_limit' => 30,
            'passing_score' => 60,
            'is_active' => true,
            'attempts_allowed' => 0,
            'show_correct_answers' => true,
        ]);

        // Section I: Salitang-ugat (Questions 1-5)
        $questions1 = [
            [
                'question' => 'Ano ang salitang-ugat ng "mandirigma"?',
                'options' => [
                    'a' => 'dirigma',
                    'b' => 'digma',
                    'c' => 'rigma',
                    'd' => 'mandiri',
                ],
                'correct_answer' => 'b',
            ],
            [
                'question' => 'Ano ang salitang-ugat ng "sansinukob"?',
                'options' => [
                    'a' => 'sinukob',
                    'b' => 'sukob',
                    'c' => 'sakop',
                    'd' => 'sinukop',
                ],
                'correct_answer' => 'b',
            ],
            [
                'question' => 'Ano ang salitang-ugat ng "kaalipinan"?',
                'options' => [
                    'a' => 'kaalip',
                    'b' => 'alipin',
                    'c' => 'lipan',
                    'd' => 'ipin',
                ],
                'correct_answer' => 'b',
            ],
            [
                'question' => 'Ano ang salitang-ugat ng "kapayapaan"?',
                'options' => [
                    'a' => 'payapa',
                    'b' => 'payap',
                    'c' => 'yapa',
                    'd' => 'kapa',
                ],
                'correct_answer' => 'a',
            ],
            [
                'question' => 'Ano ang salitang-ugat ng "kinalulugdan"?',
                'options' => [
                    'a' => 'lugod',
                    'b' => 'lugda',
                    'c' => 'lulugod',
                    'd' => 'kalugod',
                ],
                'correct_answer' => 'a',
            ],
        ];

        // Section II: Uri ng Panlapi (Questions 6-10)
        $questions2 = [
            [
                'question' => 'Anong uri ng panlapi ang ginamit sa salitang "palaruan"?',
                'options' => [
                    'a' => 'Unlapi',
                    'b' => 'Gitlapi',
                    'c' => 'Hulapi',
                    'd' => 'Kabilaan',
                ],
                'correct_answer' => 'd',
            ],
            [
                'question' => 'Anong uri ng panlapi ang ginamit sa salitang "kinasuhan"?',
                'options' => [
                    'a' => 'Unlapi',
                    'b' => 'Kabilaan',
                    'c' => 'Laguhan',
                    'd' => 'Gitlapi',
                ],
                'correct_answer' => 'b',
            ],
            [
                'question' => 'Anong uri ng panlapi ang ginamit sa salitang "kaligayahan"?',
                'options' => [
                    'a' => 'Unlapi',
                    'b' => 'Hulapi',
                    'c' => 'Kabilaan',
                    'd' => 'Laguhan',
                ],
                'correct_answer' => 'c',
            ],
            [
                'question' => 'Anong uri ng panlapi ang ginamit sa salitang "pinaglantaran"?',
                'options' => [
                    'a' => 'Unlapi',
                    'b' => 'Gitlapi',
                    'c' => 'Kabilaan',
                    'd' => 'Laguhan',
                ],
                'correct_answer' => 'c',
            ],
            [
                'question' => 'Anong uri ng panlapi ang ginamit sa salitang "tupdin"?',
                'options' => [
                    'a' => 'Unlapi',
                    'b' => 'Gitlapi',
                    'c' => 'Hulapi',
                    'd' => 'Kabilaan',
                ],
                'correct_answer' => 'c',
            ],
        ];

        // Section III: Pagbabagong Morpoponemiko (Questions 11-15)
        $questions3 = [
            [
                'question' => 'Anong uri ng pagbabagong morpoponemiko ang naganap sa salitang "pambansa"?',
                'options' => [
                    'a' => 'Asimilasyon',
                    'b' => 'Pagpapalit ng Ponema',
                    'c' => 'Pagkakaltas ng Ponema',
                    'd' => 'Metatesis',
                ],
                'correct_answer' => 'a',
            ],
            [
                'question' => 'Anong uri ng pagbabagong morpoponemiko ang naganap sa salitang "damhin"?',
                'options' => [
                    'a' => 'Asimilasyon',
                    'b' => 'Pagkakaltas ng Ponema',
                    'c' => 'Paglilipat-diin',
                    'd' => 'Pagpapalit ng Ponema',
                ],
                'correct_answer' => 'b',
            ],
            [
                'question' => 'Anong uri ng pagbabagong morpoponemiko ang naganap sa salitang "nilipad"?',
                'options' => [
                    'a' => 'Paglilipat-diin',
                    'b' => 'Asimilasyon',
                    'c' => 'Pagkakaltas ng Ponema',
                    'd' => 'Metatesis',
                ],
                'correct_answer' => 'd',
            ],
            [
                'question' => 'Anong uri ng pagbabagong morpoponemiko ang naganap sa salitang "pasukin"?',
                'options' => [
                    'a' => 'Pagkakaltas ng Ponema',
                    'b' => 'Metathesis',
                    'c' => 'Pagpapalit ng Ponema',
                    'd' => 'Paglilipat-diin',
                ],
                'correct_answer' => 'd',
            ],
            [
                'question' => 'Anong uri ng pagbabagong morpoponemiko ang naganap sa salitang "tamnan"?',
                'options' => [
                    'a' => 'Asimilasyon',
                    'b' => 'Pagpapalit ng Ponema',
                    'c' => 'Pagkakaltas ng Ponema',
                    'd' => 'Metatesis',
                ],
                'correct_answer' => 'c',
            ],
        ];

        // Combine all questions
        $allQuestions = array_merge($questions1, $questions2, $questions3);

        // Insert questions with proper order
        foreach ($allQuestions as $index => $questionData) {
            QuizQuestion::create([
                'quiz_id' => $quiz1->id,
                'question' => $questionData['question'],
                'options' => $questionData['options'],
                'correct_answer' => $questionData['correct_answer'],
                'order' => $index + 1,
            ]);
        }
    }
}
