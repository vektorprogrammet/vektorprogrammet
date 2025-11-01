<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * InterviewQuestion Model (converted from Doctrine)
 *
 * @property int $id
 * @property string $question
 * @property string|null $help
 * @property string $type
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class InterviewQuestion extends Model
{
    use HasFactory;

    protected $table = 'interview_question';

    protected $fillable = [
        'question',
        'help',
        'type',
    ];

    /**
     * Get the alternatives for the interview question.
     *
     * @return HasMany
     */
    public function alternatives(): HasMany
    {
        return $this->hasMany(InterviewQuestionAlternative::class, 'question_id');
    }

    /**
     * Get the interview schemas that use this question.
     *
     * @return BelongsToMany
     */
    public function interviewSchemas(): BelongsToMany
    {
        return $this->belongsToMany(
            InterviewSchema::class,
            'interview_schemas_questions',
            'question_id',
            'schema_id'
        );
    }
}

