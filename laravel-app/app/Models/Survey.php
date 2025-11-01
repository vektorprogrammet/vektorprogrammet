<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Survey Model (converted from Doctrine)
 *
 * @property int $id
 * @property int|null $semester_id
 * @property int|null $department_id
 * @property string $name
 * @property bool $show_custom_pop_up_message
 * @property string|null $finish_page_content
 * @property bool $confidential
 * @property int $target_audience
 * @property string $survey_pop_up_message
 * @property int|null $created_at
 * @property int|null $updated_at
 */
class Survey extends Model
{
    use HasFactory;

    const SCHOOL_SURVEY = 0;
    const TEAM_SURVEY = 1;
    const ASSISTANT_SURVEY = 2;

    protected $table = 'survey';

    protected $fillable = [
        'semester_id',
        'department_id',
        'name',
        'show_custom_pop_up_message',
        'finish_page_content',
        'confidential',
        'target_audience',
        'survey_pop_up_message',
    ];

    protected $casts = [
        'show_custom_pop_up_message' => 'boolean',
        'confidential' => 'boolean',
        'target_audience' => 'integer',
    ];

    protected $attributes = [
        'confidential' => false,
        'target_audience' => 0,
        'show_custom_pop_up_message' => false,
        'survey_pop_up_message' => 'Svar på undersøkelse!',
        'finish_page_content' => '',
    ];

    /**
     * Get the semester that owns the survey.
     *
     * @return BelongsTo
     */
    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class, 'semester_id');
    }

    /**
     * Get the department that owns the survey.
     *
     * @return BelongsTo
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    /**
     * Get the survey questions for the survey.
     *
     * @return BelongsToMany
     */
    public function surveyQuestions(): BelongsToMany
    {
        return $this->belongsToMany(
            SurveyQuestion::class,
            'survey_surveys_questions',
            'survey_id',
            'question_id'
        );
    }

    /**
     * Get the surveys taken for the survey.
     *
     * @return HasMany
     */
    public function surveysTaken(): HasMany
    {
        return $this->hasMany(SurveyTaken::class, 'survey_id');
    }

    /**
     * Get finish page content or default message.
     */
    public function getFinishPageContent(): string
    {
        if ($this->finish_page_content === null) {
            return "Takk for svaret!";
        }

        return $this->finish_page_content;
    }

    /**
     * Check if survey is confidential.
     */
    public function isConfidential(): bool
    {
        return $this->confidential;
    }

    /**
     * Set survey pop up message with default fallback.
     */
    public function setSurveyPopUpMessage(?string $message): void
    {
        if ($message === null) {
            $message = "Svar på undersøkelse!";
        }

        $this->survey_pop_up_message = $message;
    }

    /**
     * String representation.
     */
    public function __toString(): string
    {
        $str = $this->name;
        if ($this->department) {
            $str = $str . ", " . $this->department;
        }
        return $str;
    }
}

