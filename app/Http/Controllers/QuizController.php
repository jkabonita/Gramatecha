<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\QuizAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class QuizController extends Controller
{
    /**
     * Display a listing of quizzes.
     */
    public function index(): Response
    {
        $quizzes = Quiz::where('is_active', true)
            ->withCount('questions')
            ->get();

        return Inertia::render('Quizzes/Index', [
            'quizzes' => $quizzes,
        ]);
    }

    /**
     * Display a specific quiz.
     */
    public function show(Quiz $quiz): Response
    {
        $quiz->load('questions');
        $quiz->loadCount('questions');

        return Inertia::render('Quizzes/Show', [
            'quiz' => $quiz,
            'canTakeQuiz' => true,
        ]);
    }

    /**
     * Start a new quiz attempt.
     */
    public function start(Request $request, Quiz $quiz)
    {
        $request->validate([
            'student_name' => 'required|string|max:255',
        ]);

        $quiz->load('questions');

        return Inertia::render('Quizzes/Take', [
            'quiz' => $quiz,
            'studentName' => $request->input('student_name'),
        ]);
    }

    /**
     * Submit quiz answers and calculate score.
     */
    public function submit(Request $request, Quiz $quiz)
    {
        $request->validate([
            'answers' => 'required|array',
            'started_at' => 'required|date',
            'student_name' => 'required|string|max:255',
        ]);

        $quiz->load('questions');
        $answers = $request->input('answers');
        $correctAnswers = 0;
        $totalQuestions = $quiz->questions->count();

        foreach ($quiz->questions as $question) {
            $userAnswer = $answers[$question->id] ?? null;
            if ($userAnswer === $question->correct_answer) {
                $correctAnswers++;
            }
        }

        $score = round(($correctAnswers / $totalQuestions) * 100);

        $attempt = QuizAttempt::create([
            'quiz_id' => $quiz->id,
            'user_id' => Auth::id(), // Will be null for public users
            'student_name' => $request->input('student_name'),
            'answers' => $answers,
            'score' => $score,
            'correct_answers' => $correctAnswers,
            'total_questions' => $totalQuestions,
            'started_at' => $request->input('started_at'),
            'completed_at' => now(),
        ]);

        return redirect()->route('quizzes.results', ['quiz' => $quiz->slug, 'attempt' => $attempt->id])
            ->with('success', 'Quiz submitted successfully!');
    }

    /**
     * Display quiz results.
     */
    public function results(Quiz $quiz, QuizAttempt $attempt): Response
    {
        // Allow access if no user is logged in (public access)
        // Or if the attempt belongs to the authenticated user
        if (Auth::check() && $attempt->user_id !== null && $attempt->user_id !== Auth::id()) {
            abort(403);
        }

        $quiz->load('questions');
        $attempt->load('quiz');

        return Inertia::render('Quizzes/Results', [
            'quiz' => $quiz,
            'attempt' => $attempt,
        ]);
    }

    /**
     * Display all quiz attempts for admin.
     */
    public function adminAttempts()
    {
        $attempts = QuizAttempt::with(['quiz', 'user'])
            ->latest()
            ->paginate(20);

        return Inertia::render('Admin/Quizzes/Attempts', [
            'attempts' => $attempts,
        ]);
    }

    /**
     * Display quiz attempt details for admin.
     */
    public function adminAttemptDetails(QuizAttempt $attempt)
    {
        $attempt->load(['quiz.questions', 'user']);

        return Inertia::render('Admin/Quizzes/AttemptDetails', [
            'attempt' => $attempt,
            'quiz' => $attempt->quiz,
        ]);
    }
}
