<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'question_text',
        'options',
        'correct_option',
        'difficulty',
    ];

    protected function casts(): array
    {
        return [
            'options' => 'array',
        ];
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Retourne la question sans révéler la bonne réponse (pour l'API côté joueur).
     */
    public function toPublicArray(): array
    {
        return [
            'id' => $this->id,
            'question_text' => $this->question_text,
            'options' => $this->options,
            'difficulty' => $this->difficulty,
        ];
    }
}
