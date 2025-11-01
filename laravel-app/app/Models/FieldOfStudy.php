<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * FieldOfStudy Model (converted from Doctrine)
 *
 * @property int $id
 * @property string $name
 * @property string $short_name
 * @property int|null $department_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class FieldOfStudy extends Model
{
    use HasFactory;

    protected $table = 'field_of_study';

    protected $fillable = [
        'name',
        'short_name',
        'department_id',
    ];

    /**
     * Get the department that owns the field of study.
     *
     * @return BelongsTo
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    /**
     * Get the users for the field of study.
     *
     * @return HasMany
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'field_of_study_id');
    }

    /**
     * String representation.
     */
    public function __toString(): string
    {
        return $this->short_name ?? '';
    }
}

