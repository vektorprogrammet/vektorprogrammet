<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

/**
 * AssistantHistory Model (converted from Doctrine)
 *
 * @property int $id
 * @property int $user_id
 * @property int|null $semester_id
 * @property int|null $department_id
 * @property int|null $school_id
 * @property string $workdays
 * @property string|null $bolk
 * @property string $day
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class AssistantHistory extends Model
{
    use HasFactory;

    protected $table = 'assistant_history';

    protected $fillable = [
        'user_id',
        'semester_id',
        'department_id',
        'school_id',
        'workdays',
        'bolk',
        'day',
    ];

    /**
     * Get the user that owns the assistant history.
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the semester that owns the assistant history.
     *
     * @return BelongsTo
     */
    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class, 'semester_id');
    }

    /**
     * Get the department that owns the assistant history.
     *
     * @return BelongsTo
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    /**
     * Get the school that owns the assistant history.
     *
     * @return BelongsTo
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class, 'school_id');
    }

    /**
     * Check if active in group.
     */
    public function activeInGroup(string $group): bool
    {
        return strpos($this->bolk ?? '', "Bolk $group") !== false;
    }
}

