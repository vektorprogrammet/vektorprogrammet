<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

/**
 * SocialEvent Model (converted from Doctrine)
 *
 * @property int $id
 * @property int|null $department_id
 * @property int|null $semester_id
 * @property string $title
 * @property string|null $description
 * @property Carbon $start_time
 * @property Carbon $end_time
 * @property int|null $role_id
 * @property string|null $link
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class SocialEvent extends Model
{
    use HasFactory;

    protected $table = 'event';

    protected $fillable = [
        'department_id',
        'semester_id',
        'title',
        'description',
        'start_time',
        'end_time',
        'role_id',
        'link',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    /**
     * Get the department that owns the social event.
     *
     * @return BelongsTo
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    /**
     * Get the semester that owns the social event.
     *
     * @return BelongsTo
     */
    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class, 'semester_id');
    }

    /**
     * Get the role.
     *
     * @return BelongsTo
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id');
    }
}

