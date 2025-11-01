<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

/**
 * SurveyNotificationCollection Model (converted from Doctrine)
 *
 * @property int $id
 * @property string $name
 * @property int $survey_id
 * @property Carbon $time_of_notification
 * @property int $notification_type
 * @property bool $all_sent
 * @property bool $active
 * @property string $sms_message
 * @property string $email_from_name
 * @property string $email_subject
 * @property string $email_message
 * @property string $email_end_message
 * @property int $email_type
 * @property int $number_user_groups
 * @property array|null $assistant_bolks
 * @property bool $deletable
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class SurveyNotificationCollection extends Model
{
    use HasFactory;

    const EMAIL_NOTIFICATION = 0;
    const SMS_NOTIFICATION = 1;

    protected $table = 'survey_notification_collection';

    protected $fillable = [
        'name',
        'survey_id',
        'time_of_notification',
        'notification_type',
        'all_sent',
        'active',
        'sms_message',
        'email_from_name',
        'email_subject',
        'email_message',
        'email_end_message',
        'email_type',
        'number_user_groups',
        'assistant_bolks',
        'deletable',
    ];

    protected $casts = [
        'time_of_notification' => 'datetime',
        'notification_type' => 'integer',
        'all_sent' => 'boolean',
        'active' => 'boolean',
        'email_type' => 'integer',
        'number_user_groups' => 'integer',
        'assistant_bolks' => 'array',
        'deletable' => 'boolean',
    ];

    protected $attributes = [
        'name' => '',
        'all_sent' => false,
        'active' => false,
        'notification_type' => self::EMAIL_NOTIFICATION,
        'sms_message' => 'Vi i vektor jobber kontinuerlig for å forbedre assistentopplevelsen, men for å kunne gjøre det er vi avhengig tilbakemelding. Svar på følgende undersøkelse og vær med i trekning av flotte premier da vel!',
        'email_message' => 'Vi i vektor jobber kontinuerlig for å forbedre assistentopplevelsen, men for å kunne gjøre det er vi avhengig tilbakemelding. Svar på følgende undersøkelse og vær med i trekning av flotte premier da vel!',
        'email_end_message' => '<p>Med vennlig hilsen,<br/>Vektorevaluering</p>',
        'email_subject' => 'Undersøkelse fra Vektor',
        'email_type' => 0,
        'number_user_groups' => 2,
        'deletable' => true,
        'email_from_name' => 'Vektorprogrammet',
    ];

    /**
     * Boot the model.
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($collection): void {
            if (empty($collection->time_of_notification)) {
                $collection->time_of_notification = Carbon::tomorrow();
            }
        });
    }

    /**
     * Get the survey that owns the notification collection.
     *
     * @return BelongsTo
     */
    public function survey(): BelongsTo
    {
        return $this->belongsTo(Survey::class, 'survey_id');
    }

    /**
     * Get the user groups for the notification collection.
     *
     * @return BelongsToMany
     */
    public function userGroups(): BelongsToMany
    {
        return $this->belongsToMany(UserGroup::class, 'survey_notification_collection_usergroup', 'collection_id', 'usergroup_id');
    }

    /**
     * Get the survey notifications for the collection.
     *
     * @return HasMany
     */
    public function surveyNotifications(): HasMany
    {
        return $this->hasMany(SurveyNotification::class, 'survey_notification_collection_id');
    }

    /**
     * Get the teams for the notification collection.
     *
     * @return BelongsToMany
     */
    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class, 'survey_notification_collection_team', 'collection_id', 'team_id');
    }

    /**
     * Get the semesters for the notification collection.
     *
     * @return BelongsToMany
     */
    public function semesters(): BelongsToMany
    {
        return $this->belongsToMany(Semester::class, 'survey_notification_collection_semester', 'collection_id', 'semester_id');
    }

    /**
     * Get the users for the notification collection.
     *
     * @return BelongsToMany
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'survey_notification_collection_user', 'collection_id', 'user_id');
    }

    /**
     * Get the assistant departments for the notification collection.
     *
     * @return BelongsToMany
     */
    public function assistantsDepartments(): BelongsToMany
    {
        return $this->belongsToMany(Department::class, 'survey_notification_collection_department', 'collection_id', 'department_id');
    }

    /**
     * Get number of total users across all user groups.
     */
    public function getNumberTotalUsers(): ?int
    {
        $numberUsers = 0;
        foreach ($this->userGroups as $userGroup) {
            $numberUsers += $userGroup->users()->count();
        }
        return $numberUsers;
    }
}

