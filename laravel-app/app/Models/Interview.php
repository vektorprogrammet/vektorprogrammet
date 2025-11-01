<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

/**
 * Interview Model (converted from Doctrine)
 *
 * @property int $id
 * @property bool $interviewed
 * @property Carbon|null $scheduled
 * @property Carbon|null $last_schedule_changed
 * @property string|null $room
 * @property string|null $campus
 * @property string|null $map_link
 * @property Carbon|null $conducted
 * @property int|null $schema_id
 * @property int|null $interviewer_id
 * @property int|null $co_interviewer_id
 * @property int|null $interview_score_id
 * @property int $interview_status
 * @property int|null $user_id
 * @property int|null $application_id
 * @property string|null $response_code
 * @property string|null $cancel_message
 * @property string $new_time_message
 * @property int $num_accept_interview_reminders_sent
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class Interview extends Model
{
    use HasFactory;

    const STATUS_NO_CONTACT = 0;
    const STATUS_PENDING = 1;
    const STATUS_ACCEPTED = 2;
    const STATUS_REQUEST_NEW_TIME = 3;
    const STATUS_CANCELLED = 4;

    protected $table = 'interview';

    protected $fillable = [
        'interviewed',
        'scheduled',
        'last_schedule_changed',
        'room',
        'campus',
        'map_link',
        'conducted',
        'schema_id',
        'interviewer_id',
        'co_interviewer_id',
        'interview_score_id',
        'interview_status',
        'user_id',
        'response_code',
        'cancel_message',
        'new_time_message',
        'num_accept_interview_reminders_sent',
    ];

    protected $casts = [
        'interviewed' => 'boolean',
        'scheduled' => 'datetime',
        'last_schedule_changed' => 'datetime',
        'conducted' => 'datetime',
        'interview_status' => 'integer',
        'num_accept_interview_reminders_sent' => 'integer',
    ];

    protected $attributes = [
        'interviewed' => false,
        'interview_status' => self::STATUS_NO_CONTACT,
        'new_time_message' => '',
        'num_accept_interview_reminders_sent' => 0,
    ];

    /**
     * Boot the model.
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($interview): void {
            if (empty($interview->conducted)) {
                $interview->conducted = Carbon::now();
            }
        });
    }

    /**
     * Get the user that is being interviewed.
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the interviewer.
     *
     * @return BelongsTo
     */
    public function interviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'interviewer_id');
    }

    /**
     * Get the co-interviewer.
     *
     * @return BelongsTo
     */
    public function coInterviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'co_interviewer_id');
    }

    /**
     * Get the interview schema.
     *
     * @return BelongsTo
     */
    public function interviewSchema(): BelongsTo
    {
        return $this->belongsTo(InterviewSchema::class, 'schema_id');
    }

    /**
     * Get the interview score.
     *
     * @return BelongsTo
     */
    public function interviewScore(): BelongsTo
    {
        return $this->belongsTo(InterviewScore::class, 'interview_score_id');
    }

    /**
     * Get the application (inverse of Application->interview).
     * Application table has interview_id column that points to Interview.id
     *
     * @return HasOne
     */
    public function application(): HasOne
    {
        return $this->hasOne(Application::class, 'interview_id', 'id');
    }

    /**
     * Get the interview answers.
     *
     * @return HasMany
     */
    public function interviewAnswers(): HasMany
    {
        return $this->hasMany(InterviewAnswer::class, 'interview_id');
    }

    /**
     * Get interview score.
     */
    public function getScore(): int
    {
        if (!$this->interviewScore) {
            return 0;
        }

        return $this->interviewScore->sum ?? 0;
    }

    /**
     * Check if interview is pending.
     */
    public function isPending(): bool
    {
        return $this->interview_status === self::STATUS_PENDING;
    }

    /**
     * Check if interview is cancelled.
     */
    public function isCancelled(): bool
    {
        return $this->interview_status === self::STATUS_CANCELLED;
    }

    /**
     * Check if interview is a draft.
     */
    public function isDraft(): bool
    {
        return !$this->interviewed && $this->interviewScore !== null;
    }

    /**
     * Accept interview.
     */
    public function acceptInterview(): void
    {
        $this->interview_status = self::STATUS_ACCEPTED;
    }

    /**
     * Request new time.
     */
    public function requestNewTime(): void
    {
        $this->interview_status = self::STATUS_REQUEST_NEW_TIME;
    }

    /**
     * Cancel interview.
     */
    public function cancel(): void
    {
        $this->interview_status = self::STATUS_CANCELLED;
    }

    /**
     * Reset status.
     */
    public function resetStatus(): void
    {
        $this->interview_status = self::STATUS_PENDING;
    }

    /**
     * Set scheduled time.
     */
    public function setScheduled($scheduled): void
    {
        $this->scheduled = $scheduled;
        $this->last_schedule_changed = Carbon::now();
    }

    /**
     * Generate and set response code.
     */
    public function generateAndSetResponseCode(): string
    {
        $newResponseCode = bin2hex(openssl_random_pseudo_bytes(12));
        $this->response_code = $newResponseCode;

        return $newResponseCode;
    }

    /**
     * Increment number of accept-interview reminders sent.
     */
    public function incrementNumAcceptInterviewRemindersSent(): void
    {
        $this->num_accept_interview_reminders_sent++;
    }

    /**
     * Get interview status as string.
     */
    public function getInterviewStatusAsString(): string
    {
        return match ($this->interview_status) {
            self::STATUS_NO_CONTACT => 'Ikke satt opp',
            self::STATUS_PENDING => 'Ingen svar',
            self::STATUS_ACCEPTED => 'Akseptert',
            self::STATUS_REQUEST_NEW_TIME => 'Ny tid ønskes',
            self::STATUS_CANCELLED => 'Kansellert',
            default => 'Ingen svar',
        };
    }

    /**
     * Get interview status as color.
     */
    public function getInterviewStatusAsColor(): string
    {
        return match ($this->interview_status) {
            self::STATUS_NO_CONTACT => '#9999ff',
            self::STATUS_PENDING => '#0d97c4',
            self::STATUS_ACCEPTED => '#32CD32',
            self::STATUS_REQUEST_NEW_TIME => '#F08A24',
            self::STATUS_CANCELLED => '#f40f0f',
            default => '#000000',
        };
    }

    /**
     * Get cancel message or empty string.
     */
    public function getCancelMessage(): string
    {
        return $this->cancel_message ?? '';
    }
}

