<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * SurveyQuestion Model (converted from Doctrine)
 *
 * @property int $id
 * @property string $question
 * @property bool $optional
 * @property string|null $help
 * @property string $type
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class SurveyQuestion extends Model
{
    use HasFactory;

    protected $table = 'survey_question';

    protected $fillable = [
        'question',
        'optional',
        'help',
        'type',
    ];

    protected $casts = [
        'optional' => 'boolean',
    ];

    protected $attributes = [
        'optional' => false,
    ];

    /**
     * Get the alternatives for the survey question.
     *
     * @return HasMany
     */
    public function alternatives(): HasMany
    {
        return $this->hasMany(SurveyQuestionAlternative::class, 'question_id');
    }

    /**
     * Get the answers for the survey question.
     *
     * @return HasMany
     */
    public function answers(): HasMany
    {
        return $this->hasMany(SurveyAnswer::class, 'question_id');
    }

    /**
     * Get the surveys that use this question.
     *
     * @return BelongsToMany
     */
    public function surveys(): BelongsToMany
    {
        return $this->belongsToMany(
            Survey::class,
            'survey_surveys_questions',
            'question_id',
            'survey_id'
        );
    }
}

