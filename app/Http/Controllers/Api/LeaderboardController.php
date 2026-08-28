<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class LeaderboardController extends Controller
{
    /**
     * Classement global : score cumulé + nombre de quiz joués, tous joueurs confondus.
     */
    public function global()
    {
        $ranking = User::query()
            ->select('users.id', 'users.name', 'users.avatar')
            ->selectRaw('COALESCE(SUM(participations.score), 0) as total_score')
            ->selectRaw('COUNT(DISTINCT participations.id) as quizzes_played')
            ->leftJoin('participations', function ($join) {
                $join->on('participations.user_id', '=', 'users.id')
                    ->whereNotNull('participations.finished_at');
            })
            ->groupBy('users.id', 'users.name', 'users.avatar')
            ->orderByDesc('total_score')
            ->get()
            ->values()
            ->map(function ($row, $index) {
                return [
                    'rank' => $index + 1,
                    'user' => [
                        'id' => $row->id,
                        'name' => $row->name,
                        'avatar' => $row->avatar,
                    ],
                    'total_score' => (int) $row->total_score,
                    'quizzes_played' => (int) $row->quizzes_played,
                ];
            });

        return response()->json(['leaderboard' => $ranking]);
    }
}
