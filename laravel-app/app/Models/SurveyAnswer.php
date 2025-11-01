<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * SurveyAnswer Model (converted from Doctrine)
 *
 * @property int $id
 * @property int $question_id
 * @property string|null $answer
 * @property array|null $answer_array
 * @property int $survey_taken_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class SurveyAnswer extends Model
{
    use HasFactory;

    protected $table = 'survey_answer';

    protected $fillable = [
        'question_id',
        'answer',
        'answer_array',
        'survey_taken_id',
    ];

    protected $casts = [
        'answer_array' => 'array',
    ];

    protected $attributes = [
        'answer_array' => [],
    ];

    /**
     * Get the survey question that owns the answer.
     *
     * @return BelongsTo
     */
    public function surveyQuestion(): BelongsTo
    {
        return $this->belongsTo(SurveyQuestion::class, 'question_id');
    }

    /**
     * Get the survey taken that owns the answer.
     *
     * @return BelongsTo
     */
    public function surveyTaken(): BelongsTo
    {
        return $this->belongsTo(SurveyTaken::class, 'survey_taken_id');
    }
}

