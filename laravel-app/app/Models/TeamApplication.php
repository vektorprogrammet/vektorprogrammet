<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * TeamApplication Model (converted from Doctrine)
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $field_of_study
 * @property string $year_of_study
 * @property string $motivation_text
 * @property string $biography
 * @property int $team_id
 * @property string $phone
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class TeamApplication extends Model
{
    use HasFactory;

    protected $table = 'team_application';

    protected $fillable = [
        'name',
        'email',
        'field_of_study',
        'year_of_study',
        'motivation_text',
        'biography',
        'team_id',
        'phone',
    ];

    /**
     * Get the team that owns the team application.
     *
     * @return BelongsTo
     */
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'team_id');
    }
}

