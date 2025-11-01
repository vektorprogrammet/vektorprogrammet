<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

/**
 * SurveyTaken Model (converted from Doctrine)
 *
 * @property int $id
 * @property int|null $user_id
 * @property Carbon $time
 * @property int|null $school_id
 * @property int $survey_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class SurveyTaken extends Model
{
    use HasFactory;

    protected $table = 'survey_taken';

    protected $fillable = [
        'user_id',
        'time',
        'school_id',
        'survey_id',
    ];

    protected $casts = [
        'time' => 'datetime',
    ];

    /**
     * Boot the model.
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($surveyTaken): void {
            if (empty($surveyTaken->time)) {
                $surveyTaken->time = Carbon::now();
            }
        });
    }

    /**
     * Get the user that owns the survey taken.
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the school.
     *
     * @return BelongsTo
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class, 'school_id');
    }

    /**
     * Get the survey that owns the survey taken.
     *
     * @return BelongsTo
     */
    public function survey(): BelongsTo
    {
        return $this->belongsTo(Survey::class, 'survey_id');
    }

    /**
     * Get the survey answers for the survey taken.
     *
     * @return HasMany
     */
    public function surveyAnswers(): HasMany
    {
        return $this->hasMany(SurveyAnswer::class, 'survey_taken_id');
    }
}

