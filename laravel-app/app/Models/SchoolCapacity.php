<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * SchoolCapacity Model (converted from Doctrine)
 *
 * @property int $id
 * @property int $school_id
 * @property int $semester_id
 * @property int $department_id
 * @property int $monday
 * @property int $tuesday
 * @property int $wednesday
 * @property int $thursday
 * @property int $friday
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class SchoolCapacity extends Model
{
    use HasFactory;

    protected $table = 'school_capacity';

    protected $fillable = [
        'school_id',
        'semester_id',
        'department_id',
        'monday',
        'tuesday',
        'wednesday',
        'thursday',
        'friday',
    ];

    protected $attributes = [
        'monday' => 0,
        'tuesday' => 0,
        'wednesday' => 0,
        'thursday' => 0,
        'friday' => 0,
    ];

    /**
     * Get the school that owns the school capacity.
     *
     * @return BelongsTo
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class, 'school_id');
    }

    /**
     * Get the semester that owns the school capacity.
     *
     * @return BelongsTo
     */
    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class, 'semester_id');
    }

    /**
     * Get the department that owns the school capacity.
     *
     * @return BelongsTo
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id');
    }
}

