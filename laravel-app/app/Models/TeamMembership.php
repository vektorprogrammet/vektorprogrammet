<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

/**
 * TeamMembership Model (converted from Doctrine)
 *
 * @property int $id
 * @property int $user_id
 * @property int|null $team_id
 * @property int $start_semester_id
 * @property int|null $end_semester_id
 * @property string|null $deleted_team_name
 * @property bool $is_team_leader
 * @property bool $is_suspended
 * @property int|null $position_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class TeamMembership extends Model
{
    use HasFactory;

    protected $table = 'team_membership';

    protected $fillable = [
        'user_id',
        'team_id',
        'start_semester_id',
        'end_semester_id',
        'deleted_team_name',
        'is_team_leader',
        'is_suspended',
        'position_id',
    ];

    protected $casts = [
        'is_team_leader' => 'boolean',
        'is_suspended' => 'boolean',
    ];

    protected $attributes = [
        'is_team_leader' => false,
        'is_suspended' => false,
    ];

    /**
     * Get the user that owns the team membership.
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the team that owns the membership.
     *
     * @return BelongsTo
     */
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'team_id');
    }

    /**
     * Get the start semester.
     *
     * @return BelongsTo
     */
    public function startSemester(): BelongsTo
    {
        return $this->belongsTo(Semester::class, 'start_semester_id');
    }

    /**
     * Get the end semester.
     *
     * @return BelongsTo
     */
    public function endSemester(): BelongsTo
    {
        return $this->belongsTo(Semester::class, 'end_semester_id');
    }

    /**
     * Get the position.
     *
     * @return BelongsTo
     */
    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class, 'position_id');
    }

    /**
     * Check if membership is active in given semester.
     */
    public function isActiveInSemester(Semester $semester): bool
    {
        if (!$this->startSemester) {
            return false;
        }

        $started = $this->startSemester->isBefore($semester) || $this->startSemester->id === $semester->id;
        
        if (!$started) {
            return false;
        }

        if ($this->end_semester_id === null) {
            return true;
        }

        if (!$this->endSemester) {
            return true;
        }

        return $this->endSemester->isAfter($semester) || $this->endSemester->id === $semester->id;
    }

    /**
     * Check if membership is active.
     */
    public function isActive(): bool
    {
        $now = Carbon::now();
        
        if (!$this->startSemester) {
            return false;
        }

        $termStartedInPast = $this->startSemester->getStartDate() < $now;
        
        if (!$termStartedInPast) {
            return false;
        }

        if ($this->end_semester_id === null) {
            return true;
        }

        if (!$this->endSemester) {
            return true;
        }

        return $this->endSemester->getEndDate() > $now;
    }

    /**
     * String representation.
     */
    public function __toString(): string
    {
        return (string) $this->id;
    }
}

