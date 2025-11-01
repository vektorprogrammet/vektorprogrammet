<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Carbon\Carbon;

/**
 * AdmissionPeriod Model (converted from Doctrine)
 * 
 * @property int $id
 * @property int $department_id
 * @property Carbon $start_date
 * @property Carbon $end_date
 * @property int|null $info_meeting_id
 * @property int $semester_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class AdmissionPeriod extends Model
{
    protected $table = 'admission_period';

    protected $fillable = [
        'department_id',
        'start_date',
        'end_date',
        'info_meeting_id',
        'semester_id',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    /**
     * Get the department that owns the admission period.
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    /**
     * Get the semester that owns the admission period.
     */
    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class, 'semester_id');
    }

    /**
     * Get the info meeting for the admission period.
     */
    public function infoMeeting(): HasOne
    {
        return $this->hasOne(InfoMeeting::class, 'id', 'info_meeting_id');
    }

    /**
     * Check if admission period is active (semester is active).
     */
    public function isActive(): bool
    {
        $now = Carbon::now();
        $semester = $this->semester;
        
        if (!$semester) {
            return false;
        }

        return $semester->getStartDate() < $now && $now <= $semester->getEndDate();
    }

    /**
     * Check if admission period has active admission (within start/end dates).
     */
    public function hasActiveAdmission(): bool
    {
        $now = Carbon::now();
        return $this->start_date <= $now && $now <= $this->end_date;
    }

    /**
     * Check if info meeting notifications should be sent.
     */
    public function shouldSendInfoMeetingNotifications(): bool
    {
        if (!$this->infoMeeting) {
            return false;
        }

        $infoMeeting = $this->infoMeeting;
        
        if (!$infoMeeting->date || !$infoMeeting->show_on_page) {
            return false;
        }

        $now = Carbon::now();
        $meetingDate = Carbon::parse($infoMeeting->date);
        
        return $meetingDate->isToday() && $meetingDate->isFuture();
    }

    /**
     * String representation.
     */
    public function __toString(): string
    {
        $semesterName = $this->semester ? $this->semester->getName() : '';
        $departmentName = $this->department ? (string) $this->department : '';
        return $semesterName . ' - ' . $departmentName;
    }
}

