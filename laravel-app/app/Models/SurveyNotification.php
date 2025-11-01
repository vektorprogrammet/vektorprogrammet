<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

/**
 * SurveyNotification Model (converted from Doctrine)
 *
 * @property int $id
 * @property int|null $user_id
 * @property string $user_identifier
 * @property int|null $survey_notification_collection_id
 * @property Carbon|null $time_notification_sent
 * @property bool $sent
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class SurveyNotification extends Model
{
    use HasFactory;

    protected $table = 'survey_notification';

    protected $fillable = [
        'user_id',
        'user_identifier',
        'survey_notification_collection_id',
        'time_notification_sent',
        'sent',
    ];

    protected $casts = [
        'time_notification_sent' => 'datetime',
        'sent' => 'boolean',
    ];

    protected $attributes = [
        'sent' => false,
    ];

    /**
     * Boot the model.
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($notification): void {
            if (empty($notification->user_identifier)) {
                $notification->user_identifier = bin2hex(openssl_random_pseudo_bytes(12));
            }
        });
    }

    /**
     * Get the user.
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the survey notification collection.
     *
     * @return BelongsTo
     */
    public function surveyNotificationCollection(): BelongsTo
    {
        return $this->belongsTo(SurveyNotificationCollection::class, 'survey_notification_collection_id');
    }

    /**
     * Get the survey link clicks for the notification.
     *
     * @return HasMany
     */
    public function surveyLinkClicks(): HasMany
    {
        return $this->hasMany(SurveyLinkClick::class, 'notification_id');
    }
}

