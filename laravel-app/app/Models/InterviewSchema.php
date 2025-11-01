<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * InterviewSchema Model (converted from Doctrine)
 *
 * @property int $id
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class InterviewSchema extends Model
{
    use HasFactory;

    protected $table = 'interview_schema';

    protected $fillable = [
        'name',
    ];

    /**
     * Get the interview questions for the schema.
     *
     * @return BelongsToMany
     */
    public function interviewQuestions(): BelongsToMany
    {
        return $this->belongsToMany(
            InterviewQuestion::class,
            'interview_schemas_questions',
            'schema_id',
            'question_id'
        );
    }
}

