<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * AccessRule Model (converted from Doctrine)
 *
 * @property int $id
 * @property string $name
 * @property string $resource
 * @property string $method
 * @property bool $is_routing_rule
 * @property bool $for_executive_board
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class AccessRule extends Model
{
    use HasFactory;

    protected $table = 'access_rule';

    protected $fillable = [
        'name',
        'resource',
        'method',
        'is_routing_rule',
        'for_executive_board',
    ];

    protected $casts = [
        'is_routing_rule' => 'boolean',
        'for_executive_board' => 'boolean',
    ];

    protected $attributes = [
        'is_routing_rule' => false,
        'for_executive_board' => false,
        'method' => 'GET',
    ];

    /**
     * Get the users for the access rule.
     *
     * @return BelongsToMany
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'access_rule_user', 'access_rule_id', 'user_id');
    }

    /**
     * Get the teams for the access rule.
     *
     * @return BelongsToMany
     */
    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class, 'access_rule_team', 'access_rule_id', 'team_id');
    }

    /**
     * Get the roles for the access rule.
     *
     * @return BelongsToMany
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'access_rule_role', 'access_rule_id', 'role_id');
    }
}

