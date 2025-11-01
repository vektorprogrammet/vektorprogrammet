<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

/**
 * Semester Model (converted from Doctrine)
 * 
 * @property int $id
 * @property string $semester_time
 * @property string $year
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class Semester extends Model
{
    protected $table = 'semester';

    protected $fillable = [
        'semester_time',
        'year',
    ];

    /**
     * Get the admission periods for the semester.
     */
    public function admissionPeriods(): HasMany
    {
        return $this->hasMany(AdmissionPeriod::class, 'semester_id');
    }

    /**
     * Get the semester name.
     */
    public function getName(): string
    {
        return $this->semester_time . ' ' . $this->year;
    }

    /**
     * Get semester start date.
     */
    public function getStartDate(): Carbon
    {
        $startMonth = $this->semester_time == 'Vår' ? '01' : '08';
        return Carbon::create($this->year, $startMonth, 1, 0, 0, 0);
    }

    /**
     * Get semester end date.
     */
    public function getEndDate(): Carbon
    {
        $endMonth = $this->semester_time == 'Vår' ? '07' : '12';
        return Carbon::create($this->year, $endMonth, 31, 23, 59, 59);
    }

    /**
     * Check if semester is active.
     */
    public function isActive(): bool
    {
        $now = Carbon::now();
        return $this->getStartDate() < $now && $now <= $this->getEndDate();
    }

    /**
     * Checks if this semester is between the bounds $semesterPrevious and $semesterLater.
     * 
     * Note: This range comparison is weak, meaning the semester can count as
     * being inBetween even though it is equal to one or both of the semester bounds.
     * Furthermore, the semester bounds can be null, which implies the range
     * extends infinitely far into the past or into the future.
     */
    public function isBetween(?Semester $semesterPrevious, ?Semester $semesterLater): bool
    {
        return $this->isAfter($semesterPrevious) && $this->isBefore($semesterLater);
    }

    /**
     * Checks if this semester is before $semester.
     * 
     * Note: This function performs a weak comparison, meaning equal semesters count as before.
     * Furthermore, null semesters also count as before.
     */
    public function isBefore(?Semester $semester): bool
    {
        if ($semester === null) {
            return true;
        }
        if ($this->year === $semester->year) {
            return !($this->semester_time === 'Høst' && $semester->semester_time === 'Vår');
        } else {
            return $this->year < $semester->year;
        }
    }

    /**
     * Checks if this semester is after $semester.
     * 
     * Note: This function performs a weak comparison, meaning equal semesters count as after.
     * Furthermore, null semesters also count as after.
     */
    public function isAfter(?Semester $semester): bool
    {
        if ($semester === null) {
            return true;
        }
        if ($this->year === $semester->year) {
            return !($this->semester_time === 'Vår' && $semester->semester_time === 'Høst');
        } else {
            return $this->year > $semester->year;
        }
    }

    /**
     * String representation.
     */
    public function __toString(): string
    {
        return $this->getName();
    }
}

