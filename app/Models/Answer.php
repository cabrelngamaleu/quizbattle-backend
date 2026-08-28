<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    use HasFactory;

    protected $fillable = [
        'participation_id',
        'question_id',
        'selected_option',
        'is_correct',
        'time_taken_seconds',
        'points_earned',
    ];

    protected function casts(): array
    {
        return [
            'is_correct' => 'boolean',
        ];
    }

    public function participation()
    {
        return $this->belongsTo(Participation::class);
    }

    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}
