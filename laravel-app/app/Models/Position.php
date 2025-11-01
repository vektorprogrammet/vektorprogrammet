<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Position Model (converted from Doctrine)
 *
 * @property int $id
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class Position extends Model
{
    use HasFactory;

    protected $table = 'position';

    protected $fillable = [
        'name',
    ];

    /**
     * Get the team memberships for the position.
     *
     * @return HasMany
     */
    public function teamMemberships(): HasMany
    {
        return $this->hasMany(TeamMembership::class, 'position_id');
    }

    /**
     * String representation.
     */
    public function __toString(): string
    {
        return $this->name ?? '';
    }
}

