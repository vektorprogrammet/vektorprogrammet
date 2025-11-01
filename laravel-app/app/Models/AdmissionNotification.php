<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

/**
 * AdmissionNotification Model (converted from Doctrine)
 *
 * @property int $id
 * @property Carbon $timestamp
 * @property int $subscriber_id
 * @property int|null $semester_id
 * @property bool $info_meeting
 * @property int|null $department_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class AdmissionNotification extends Model
{
    use HasFactory;

    protected $table = 'admission_notification';

    protected $fillable = [
        'timestamp',
        'subscriber_id',
        'semester_id',
        'info_meeting',
        'department_id',
    ];

    protected $casts = [
        'timestamp' => 'datetime',
        'info_meeting' => 'boolean',
    ];

    protected $attributes = [
        'info_meeting' => false,
    ];

    /**
     * Boot the model.
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($notification): void {
            if (empty($notification->timestamp)) {
                $notification->timestamp = Carbon::now();
            }
        });
    }

    /**
     * Get the subscriber that owns the notification.
     *
     * @return BelongsTo
     */
    public function subscriber(): BelongsTo
    {
        return $this->belongsTo(AdmissionSubscriber::class, 'subscriber_id');
    }

    /**
     * Get the semester.
     *
     * @return BelongsTo
     */
    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class, 'semester_id');
    }

    /**
     * Get the department.
     *
     * @return BelongsTo
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id');
    }
}

