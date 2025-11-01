<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

/**
 * ExecutiveBoardMembership Model (converted from Doctrine)
 *
 * @property int $id
 * @property int $user_id
 * @property int $board_id
 * @property string|null $position_name
 * @property int $start_semester_id
 * @property int|null $end_semester_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class ExecutiveBoardMembership extends Model
{
    use HasFactory;

    protected $table = 'executive_board_membership';

    protected $fillable = [
        'user_id',
        'board_id',
        'position_name',
        'start_semester_id',
        'end_semester_id',
    ];

    protected $attributes = [
        'position_name' => '',
    ];

    /**
     * Get the user that owns the executive board membership.
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the executive board that owns the membership.
     *
     * @return BelongsTo
     */
    public function board(): BelongsTo
    {
        return $this->belongsTo(ExecutiveBoard::class, 'board_id');
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
     * Get position name (alias for position_name).
     */
    public function getPositionName(): ?string
    {
        return $this->position_name;
    }

    /**
     * Get team (returns board for interface compatibility).
     */
    public function getTeam()
    {
        return $this->board;
    }

    /**
     * Check if membership is active.
     */
    public function isActive(): bool
    {
        $now = Carbon::now();
        
        $termEndsInFuture = $this->end_semester_id === null;
        if (!$termEndsInFuture && $this->endSemester) {
            $termEndsInFuture = $this->endSemester->getEndDate() > $now;
        }
        
        $termStartedInPast = false;
        if ($this->startSemester) {
            $termStartedInPast = $this->startSemester->getStartDate() < $now;
        }
        
        return $termEndsInFuture && $termStartedInPast;
    }

    /**
     * String representation.
     */
    public function __toString(): string
    {
        return (string) $this->id;
    }
}

