<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * ExecutiveBoard Model (converted from Doctrine)
 *
 * @property int $id
 * @property string $name
 * @property string|null $email
 * @property string|null $description
 * @property string|null $short_description
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class ExecutiveBoard extends Model
{
    use HasFactory;

    protected $table = 'executive_board';

    protected $fillable = [
        'name',
        'email',
        'description',
        'short_description',
    ];

    /**
     * Get the board memberships for the executive board.
     *
     * @return HasMany
     */
    public function boardMemberships(): HasMany
    {
        return $this->hasMany(ExecutiveBoardMembership::class, 'board_id');
    }

    /**
     * Get type.
     */
    public function getType(): string
    {
        return 'executive_board';
    }

    /**
     * String representation.
     */
    public function __toString(): string
    {
        return $this->name ?? '';
    }
}

