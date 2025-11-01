<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * InterviewAnswer Model (converted from Doctrine)
 *
 * @property int $id
 * @property int $interview_id
 * @property int $question_id
 * @property array|null $answer
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class InterviewAnswer extends Model
{
    use HasFactory;

    protected $table = 'interview_answer';

    protected $fillable = [
        'interview_id',
        'question_id',
        'answer',
    ];

    protected $casts = [
        'answer' => 'array',
    ];

    /**
     * Get the interview that owns the answer.
     *
     * @return BelongsTo
     */
    public function interview(): BelongsTo
    {
        return $this->belongsTo(Interview::class, 'interview_id');
    }

    /**
     * Get the interview question that owns the answer.
     *
     * @return BelongsTo
     */
    public function interviewQuestion(): BelongsTo
    {
        return $this->belongsTo(InterviewQuestion::class, 'question_id');
    }

    /**
     * String representation.
     */
    public function __toString(): string
    {
        if (is_string($this->answer)) {
            return $this->answer;
        }
        if (!is_array($this->answer)) {
            return "";
        }

        return implode(", ", $this->answer);
    }
}

