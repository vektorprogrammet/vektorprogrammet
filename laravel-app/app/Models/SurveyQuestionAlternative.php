<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * SurveyQuestionAlternative Model (converted from Doctrine)
 *
 * @property int $id
 * @property string $alternative
 * @property int $question_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class SurveyQuestionAlternative extends Model
{
    use HasFactory;

    protected $table = 'survey_question_alternative';

    protected $fillable = [
        'alternative',
        'question_id',
    ];

    /**
     * Get the survey question that owns the alternative.
     *
     * @return BelongsTo
     */
    public function surveyQuestion(): BelongsTo
    {
        return $this->belongsTo(SurveyQuestion::class, 'question_id');
    }
}

