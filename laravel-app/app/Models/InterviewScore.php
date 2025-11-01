<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * InterviewScore Model (converted from Doctrine)
 *
 * @property int $id
 * @property int $explanatory_power
 * @property int $role_model
 * @property int $suitability
 * @property string $suitable_assistant
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class InterviewScore extends Model
{
    use HasFactory;

    protected $table = 'interview_score';

    protected $fillable = [
        'explanatory_power',
        'role_model',
        'suitability',
        'suitable_assistant',
    ];

    /**
     * Get the sum of all scores.
     */
    public function getSum(): int
    {
        return $this->explanatory_power + $this->role_model + $this->suitability;
    }

    /**
     * Hide scores (set to 0).
     */
    public function hideScores(): void
    {
        $this->explanatory_power = 0;
        $this->role_model = 0;
        $this->suitability = 0;
    }
}

