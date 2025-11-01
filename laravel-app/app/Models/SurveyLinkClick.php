<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

/**
 * SurveyLinkClick Model (converted from Doctrine)
 *
 * @property int $id
 * @property Carbon $time_of_visit
 * @property int $notification_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class SurveyLinkClick extends Model
{
    use HasFactory;

    protected $table = 'survey_link_click';

    protected $fillable = [
        'time_of_visit',
        'notification_id',
    ];

    protected $casts = [
        'time_of_visit' => 'datetime',
    ];

    /**
     * Boot the model.
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($linkClick): void {
            if (empty($linkClick->time_of_visit)) {
                $linkClick->time_of_visit = Carbon::now();
            }
        });
    }

    /**
     * Get the survey notification that owns the link click.
     *
     * @return BelongsTo
     */
    public function notification(): BelongsTo
    {
        return $this->belongsTo(SurveyNotification::class, 'notification_id');
    }
}

