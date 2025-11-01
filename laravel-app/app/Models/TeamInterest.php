<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Carbon\Carbon;

/**
 * TeamInterest Model (converted from Doctrine)
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property Carbon $timestamp
 * @property int|null $semester_id
 * @property int|null $department_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class TeamInterest extends Model
{
    use HasFactory;

    protected $table = 'team_interest';

    protected $fillable = [
        'name',
        'email',
        'timestamp',
        'semester_id',
        'department_id',
    ];

    protected $casts = [
        'timestamp' => 'datetime',
    ];

    /**
     * Boot the model.
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($teamInterest): void {
            if (empty($teamInterest->timestamp)) {
                $teamInterest->timestamp = Carbon::now();
            }
        });
    }

    /**
     * Get the semester.
     *
     * @return BelongsTo
     */
    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class, 'semester_id');
    }

    /**
     * Get the department.
     *
     * @return BelongsTo
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    /**
     * Get the potential teams for the team interest.
     *
     * @return BelongsToMany
     */
    public function potentialTeams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class, 'team_interest_team', 'team_interest_id', 'team_id');
    }
}

