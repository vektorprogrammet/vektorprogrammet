<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

/**
 * Department Model (converted from Doctrine)
 * 
 * @property int $id
 * @property string $name
 * @property string $short_name
 * @property string $email
 * @property string|null $address
 * @property string $city
 * @property string|null $latitude
 * @property string|null $longitude
 * @property string|null $slack_channel
 * @property string|null $logo_path
 * @property bool $active
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class Department extends Model
{
    protected $table = 'department';

    protected $fillable = [
        'name',
        'short_name',
        'email',
        'address',
        'city',
        'latitude',
        'longitude',
        'slack_channel',
        'logo_path',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    protected $attributes = [
        'active' => true,
    ];

    /**
     * Get the schools for the department.
     */
    public function schools(): BelongsToMany
    {
        return $this->belongsToMany(School::class, 'department_school', 'department_id', 'school_id');
    }

    /**
     * Get the fields of study for the department.
     */
    public function fieldOfStudy(): HasMany
    {
        return $this->hasMany(FieldOfStudy::class, 'department_id');
    }

    /**
     * Get the admission periods for the department.
     */
    public function admissionPeriods(): HasMany
    {
        return $this->hasMany(AdmissionPeriod::class, 'department_id')
            ->orderBy('start_date', 'desc');
    }

    /**
     * Get the teams for the department.
     */
    public function teams(): HasMany
    {
        return $this->hasMany(Team::class, 'department_id');
    }

    /**
     * Get the current admission period.
     */
    public function getCurrentAdmissionPeriod(): ?AdmissionPeriod
    {
        $now = Carbon::now();

        return $this->admissionPeriods()
            ->whereHas('semester', function ($query) use ($now) {
                $query->where('start_date', '<', $now)
                      ->where('end_date', '>', $now);
            })
            ->first();
    }

    /**
     * Get the latest admission period.
     */
    public function getLatestAdmissionPeriod(): ?AdmissionPeriod
    {
        $now = Carbon::now();

        return $this->admissionPeriods()
            ->whereHas('semester', function ($query) use ($now) {
                $query->where('start_date', '<', $now);
            })
            ->orderBy('end_date', 'desc')
            ->first();
    }

    /**
     * Get current or latest admission period.
     */
    public function getCurrentOrLatestAdmissionPeriod(): ?AdmissionPeriod
    {
        return $this->getCurrentAdmissionPeriod() ?? $this->getLatestAdmissionPeriod();
    }

    /**
     * Check if department has active admission.
     */
    public function activeAdmission(): bool
    {
        $admissionPeriod = $this->getCurrentAdmissionPeriod();
        if (!$admissionPeriod) {
            return false;
        }

        $now = Carbon::now();
        return $admissionPeriod->start_date < $now && $now < $admissionPeriod->end_date;
    }

    /**
     * Check if department is active.
     */
    public function isActive(): bool
    {
        return $this->active;
    }

    /**
     * String representation.
     */
    public function __toString(): string
    {
        return (string) $this->city;
    }
}

