<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * School Model (converted from Doctrine)
 *
 * @property int $id
 * @property string $name
 * @property string $contact_person
 * @property string $email
 * @property string $phone
 * @property bool $international
 * @property bool $active
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class School extends Model
{
    use HasFactory;

    protected $table = 'school';

    protected $fillable = [
        'name',
        'contact_person',
        'email',
        'phone',
        'international',
        'active',
    ];

    protected $casts = [
        'international' => 'boolean',
        'active' => 'boolean',
    ];

    protected $attributes = [
        'international' => false,
        'active' => true,
    ];

    /**
     * Get the departments for the school.
     *
     * @return BelongsToMany
     */
    public function departments(): BelongsToMany
    {
        return $this->belongsToMany(
            Department::class,
            'department_school',
            'school_id',
            'department_id'
        );
    }

    /**
     * Get the assistant histories for the school.
     *
     * @return HasMany
     */
    public function assistantHistories(): HasMany
    {
        return $this->hasMany(AssistantHistory::class, 'school_id');
    }

    /**
     * Get the school capacities for the school.
     *
     * @return HasMany
     */
    public function capacities(): HasMany
    {
        return $this->hasMany(SchoolCapacity::class, 'school_id');
    }

    /**
     * Check if school is international.
     */
    public function isInternational(): bool
    {
        return $this->international;
    }

    /**
     * Check if school is active.
     */
    public function isActive(): bool
    {
        return $this->active;
    }

    /**
     * Check if school belongs to department.
     */
    public function belongsToDepartment(Department $department): bool
    {
        return $this->departments()->where('id', $department->id)->exists();
    }

    /**
     * String representation.
     */
    public function __toString(): string
    {
        return $this->name ?? '';
    }
}

