<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Role Model (converted from Doctrine)
 *
 * @property int $id
 * @property string $name
 * @property string $role
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class Role extends Model
{
    use HasFactory;

    protected $table = 'role';

    protected $fillable = [
        'name',
        'role',
    ];

    /**
     * Get the users for the role.
     *
     * @return BelongsToMany
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_role', 'role_id', 'user_id');
    }

    /**
     * Get role string.
     */
    public function getRole(): string
    {
        return $this->role;
    }

    /**
     * String representation.
     */
    public function __toString(): string
    {
        return $this->name ?? '';
    }
}

