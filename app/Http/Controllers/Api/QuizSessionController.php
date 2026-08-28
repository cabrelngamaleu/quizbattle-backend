<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Answer;
use App\Models\Participation;
use App\Models\Question;
use App\Models\QuizSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class QuizSessionController extends Controller
{
    // Barème de points : réponse correcte rapide = plus de points
    private const MAX_POINTS = 100;
    private const MIN_POINTS = 20;
    private const POINTS_LOST_PER_SECOND = 4;

    /**
     * Crée une nouvelle session de quiz et tire aléatoirement les questions.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|exists:categories,id',
            'questions_count' => 'nullable|integer|min:3|max:30',
            'closes_at' => 'nullable|date|after:now',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $questionsCount = $request->input('questions_count', 10);

        $questions = Question::where('category_id', $request->category_id)
            ->inRandomOrder()
            ->limit($questionsCount)
            ->get();

        if ($questions->count() < 3) {
            return response()->json([
                'message' => 'Pas assez de questions disponibles dans cette catégorie.',
            ], 422);
        }

        $session = DB::transaction(function () use ($request, $questions) {
            $session = QuizSession::create([
                'code' => QuizSession::generateUniqueCode(),
                'creator_id' => $request->user()->id,
                'category_id' => $request->category_id,
                'questions_count' => $questions->count(),
                'status' => 'open',
                'closes_at' => $request->closes_at,
            ]);

            foreach ($questions as $index => $question) {
                $session->questions()->attach($question->id, ['order' => $index + 1]);
            }

            return $session;
        });

        return response()->json([
            'session' => $session->load('category'),
            'share_code' => $session->code,
        ], 201);
    }

    /**
     * Rejoindre une session via son code -> crée la participation et renvoie les questions
     * (sans les bonnes réponses).
     */
    public function join(Request $request, string $code)
    {
        $session = QuizSession::where('code', strtoupper($code))->firstOrFail();

        if ($session->status === 'closed') {
            return response()->json(['message' => 'Cette session est fermée.'], 422);
        }

        $participation = Participation::firstOrCreate(
            [
                'quiz_session_id' => $session->id,
                'user_id' => $request->user()->id,
            ],
            ['started_at' => now()]
        );

        $questions = $session->questions()->get()->map(fn ($q) => $q->toPublicArray());

        return response()->json([
            'session' => $session->load('category'),
            'participation_id' => $participation->id,
            'already_finished' => ! is_null($participation->finished_at),
            'questions' => $questions,
        ]);
    }

    /**
     * Soumettre une réponse à une question de la session.
     */
    public function answer(Request $request, string $code)
    {
        $validator = Validator::make($request->all(), [
            'question_id' => 'required|exists:questions,id',
            'selected_option' => 'nullable|integer|min:0|max:5',
            'time_taken_seconds' => 'required|integer|min:0|max:120',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $session = QuizSession::where('code', strtoupper($code))->firstOrFail();

        $participation = Participation::where('quiz_session_id', $session->id)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        if ($participation->finished_at) {
            return response()->json(['message' => 'Tu as déjà terminé cette session.'], 422);
        }

        $question = Question::findOrFail($request->question_id);

        $isCorrect = $request->selected_option !== null
            && (int) $request->selected_option === $question->correct_option;

        $points = 0;
        if ($isCorrect) {
            $points = max(
                self::MIN_POINTS,
                self::MAX_POINTS - ($request->time_taken_seconds * self::POINTS_LOST_PER_SECOND)
            );
        }

        $answer = Answer::updateOrCreate(
            [
                'participation_id' => $participation->id,
                'question_id' => $question->id,
            ],
            [
                'selected_option' => $request->selected_option,
                'is_correct' => $isCorrect,
                'time_taken_seconds' => $request->time_taken_seconds,
                'points_earned' => $points,
            ]
        );

        // Recalcule le score total de la participation
        $participation->score = $participation->answers()->sum('points_earned');
        $participation->save();

        return response()->json([
            'is_correct' => $isCorrect,
            'points_earned' => $points,
            'correct_option' => $question->correct_option,
            'total_score' => $participation->score,
        ]);
    }

    /**
     * Marque la participation comme terminée.
     */
    public function finish(Request $request, string $code)
    {
        $session = QuizSession::where('code', strtoupper($code))->firstOrFail();

        $participation = Participation::where('quiz_session_id', $session->id)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $participation->finished_at = now();
        $participation->save();

        return response()->json([
            'final_score' => $participation->score,
            'rank' => $this->rankFor($session, $participation),
        ]);
    }

    /**
     * Classement d'une session précise.
     */
    public function leaderboard(string $code)
    {
        $session = QuizSession::where('code', strtoupper($code))->firstOrFail();

        $ranking = $session->participations()
            ->with('user:id,name,avatar')
            ->orderByDesc('score')
            ->orderBy('finished_at')
            ->get()
            ->values()
            ->map(function ($participation, $index) {
                return [
                    'rank' => $index + 1,
                    'user' => $participation->user,
                    'score' => $participation->score,
                    'finished' => ! is_null($participation->finished_at),
                ];
            });

        return response()->json([
            'session' => $session->only(['code', 'status', 'questions_count']),
            'leaderboard' => $ranking,
        ]);
    }

    private function rankFor(QuizSession $session, Participation $participation): int
    {
        return $session->participations()
            ->where('score', '>', $participation->score)
            ->count() + 1;
    }
}
