<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

/**
 * AdmissionSubscriber Model (converted from Doctrine)
 *
 * @property int $id
 * @property string $email
 * @property Carbon $timestamp
 * @property int|null $department_id
 * @property string $unsubscribe_code
 * @property bool $from_application
 * @property bool $info_meeting
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class AdmissionSubscriber extends Model
{
    use HasFactory;

    protected $table = 'admission_subscriber';

    protected $fillable = [
        'email',
        'timestamp',
        'department_id',
        'unsubscribe_code',
        'from_application',
        'info_meeting',
    ];

    protected $casts = [
        'timestamp' => 'datetime',
        'from_application' => 'boolean',
        'info_meeting' => 'boolean',
    ];

    protected $attributes = [
        'from_application' => false,
        'info_meeting' => false,
    ];

    /**
     * Boot the model.
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($subscriber): void {
            if (empty($subscriber->timestamp)) {
                $subscriber->timestamp = Carbon::now();
            }
            if (empty($subscriber->unsubscribe_code)) {
                $subscriber->unsubscribe_code = bin2hex(openssl_random_pseudo_bytes(12));
            }
        });
    }

    /**
     * Get the department that owns the admission subscriber.
     *
     * @return BelongsTo
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    /**
     * Get the admission notifications for the subscriber.
     *
     * @return HasMany
     */
    public function notifications(): HasMany
    {
        return $this->hasMany(AdmissionNotification::class, 'subscriber_id');
    }

    /**
     * String representation.
     */
    public function __toString(): string
    {
        return $this->department?->city ?? '';
    }
}

