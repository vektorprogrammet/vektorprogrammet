<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * UserGroup Model (converted from Doctrine)
 *
 * @property int $id
 * @property string $name
 * @property bool $active
 * @property int|null $user_group_collection_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class UserGroup extends Model
{
    use HasFactory;

    protected $table = 'usergroup';

    protected $fillable = [
        'name',
        'active',
        'user_group_collection_id',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    protected $attributes = [
        'name' => '',
        'active' => false,
    ];

    /**
     * Get the users for the user group.
     *
     * @return BelongsToMany
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'usergroup_user', 'usergroup_id', 'user_id');
    }

    /**
     * Get the user group collection that owns the user group.
     *
     * @return BelongsTo
     */
    public function userGroupCollection(): BelongsTo
    {
        return $this->belongsTo(UserGroupCollection::class, 'user_group_collection_id');
    }

    /**
     * String representation.
     */
    public function __toString(): string
    {
        return $this->name ?? '';
    }
}

