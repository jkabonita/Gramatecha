<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Quiz extends Model
{
    protected $fillable = [
        'title',
        'description',
        'slug',
        'time_limit',
        'passing_score',
        'is_active',
        'attempts_allowed',
        'show_correct_answers',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'show_correct_answers' => 'boolean',
    ];

    public function questions(): HasMany
    {
        return $this->hasMany(QuizQuestion::class)->orderBy('order');
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(QuizAttempt::class);
    }

    public function userAttempts($userId): HasMany
    {
        return $this->attempts()->where('user_id', $userId);
    }
}
