<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

/**
 * Team Model (converted from Doctrine)
 *
 * @property int $id
 * @property string $name
 * @property string|null $email
 * @property int $department_id
 * @property string|null $description
 * @property string|null $short_description
 * @property bool|null $accept_application
 * @property Carbon|null $deadline
 * @property bool $active
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class Team extends Model
{
    use HasFactory;

    protected $table = 'team';

    protected $fillable = [
        'name',
        'email',
        'department_id',
        'description',
        'short_description',
        'accept_application',
        'deadline',
        'active',
    ];

    protected $casts = [
        'accept_application' => 'boolean',
        'deadline' => 'datetime',
        'active' => 'boolean',
    ];

    protected $attributes = [
        'active' => true,
    ];

    /**
     * Get the department that owns the team.
     *
     * @return BelongsTo
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    /**
     * Get the team memberships for the team.
     *
     * @return HasMany
     */
    public function teamMemberships(): HasMany
    {
        return $this->hasMany(TeamMembership::class, 'team_id');
    }

    /**
     * Get the applications with team interest.
     *
     * @return BelongsToMany
     */
    public function potentialMembers(): BelongsToMany
    {
        return $this->belongsToMany(
            Application::class,
            'application_team',
            'team_id',
            'application_id'
        );
    }

    /**
     * Get the team applications.
     *
     * @return HasMany
     */
    public function applications(): HasMany
    {
        return $this->hasMany(TeamApplication::class, 'team_id');
    }

    /**
     * Check if team is active.
     */
    public function isActive(): bool
    {
        return $this->active;
    }

    /**
     * Check if team accepts applications and deadline is valid.
     */
    public function getAcceptApplicationAndDeadline(): bool
    {
        $now = Carbon::now();
        return ($this->accept_application && $now < $this->deadline) 
            || ($this->accept_application && $this->deadline === null);
    }

    /**
     * String representation.
     */
    public function __toString(): string
    {
        return $this->name ?? '';
    }
}

