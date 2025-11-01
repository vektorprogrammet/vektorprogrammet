<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * UserGroupCollection Model (converted from Doctrine)
 *
 * @property int $id
 * @property string $name
 * @property int $number_user_groups
 * @property array|null $assistant_bolks
 * @property bool $deletable
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class UserGroupCollection extends Model
{
    use HasFactory;

    protected $table = 'user_group_collection';

    protected $fillable = [
        'name',
        'number_user_groups',
        'assistant_bolks',
        'deletable',
    ];

    protected $casts = [
        'number_user_groups' => 'integer',
        'assistant_bolks' => 'array',
        'deletable' => 'boolean',
    ];

    protected $attributes = [
        'name' => '',
        'number_user_groups' => 2,
        'deletable' => true,
        'assistant_bolks' => [],
    ];

    /**
     * Get the user groups for the collection.
     *
     * @return HasMany
     */
    public function userGroups(): HasMany
    {
        return $this->hasMany(UserGroup::class, 'user_group_collection_id');
    }

    /**
     * Get the teams for the user group collection.
     *
     * @return BelongsToMany
     */
    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class, 'user_group_collection_team', 'collection_id', 'team_id');
    }

    /**
     * Get the semesters for the user group collection.
     *
     * @return BelongsToMany
     */
    public function semesters(): BelongsToMany
    {
        return $this->belongsToMany(Semester::class, 'user_group_collection_semester', 'collection_id', 'semester_id');
    }

    /**
     * Get the users for the user group collection.
     *
     * @return BelongsToMany
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_group_collection_user', 'collection_id', 'user_id');
    }

    /**
     * Get the assistant departments for the user group collection.
     *
     * @return BelongsToMany
     */
    public function assistantsDepartments(): BelongsToMany
    {
        return $this->belongsToMany(Department::class, 'user_group_collection_department', 'collection_id', 'department_id');
    }

    /**
     * Get number of total users across all user groups.
     */
    public function getNumberTotalUsers(): ?int
    {
        $numberUsers = 0;
        foreach ($this->userGroups as $userGroup) {
            $numberUsers += $userGroup->users()->count();
        }
        return $numberUsers;
    }

    /**
     * String representation.
     */
    public function __toString(): string
    {
        return $this->name ?? '';
    }
}

