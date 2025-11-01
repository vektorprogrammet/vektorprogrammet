<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * InterviewQuestionAlternative Model (converted from Doctrine)
 *
 * @property int $id
 * @property string $alternative
 * @property int $question_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class InterviewQuestionAlternative extends Model
{
    use HasFactory;

    protected $table = 'interview_question_alternative';

    protected $fillable = [
        'alternative',
        'question_id',
    ];

    /**
     * Get the interview question that owns the alternative.
     *
     * @return BelongsTo
     */
    public function interviewQuestion(): BelongsTo
    {
        return $this->belongsTo(InterviewQuestion::class, 'question_id');
    }
}

