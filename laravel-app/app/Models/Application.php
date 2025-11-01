<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Carbon\Carbon;

/**
 * Application Model (converted from Doctrine)
 * 
 * @property int $id
 * @property int $admission_period_id
 * @property string $year_of_study
 * @property bool $monday
 * @property bool $tuesday
 * @property bool $wednesday
 * @property bool $thursday
 * @property bool $friday
 * @property bool $substitute
 * @property string|null $language
 * @property bool $double_position
 * @property string|null $preferred_group
 * @property string|null $preferred_school
 * @property int $user_id
 * @property bool $previous_participation
 * @property Carbon $last_edited
 * @property Carbon $created
 * @property array $heard_about_from
 * @property bool $team_interest
 * @property string|null $special_needs
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class Application extends Model
{
    protected $table = 'application';

    protected $fillable = [
        'admission_period_id',
        'year_of_study',
        'monday',
        'tuesday',
        'wednesday',
        'thursday',
        'friday',
        'substitute',
        'language',
        'double_position',
        'preferred_group',
        'preferred_school',
        'user_id',
        'previous_participation',
        'last_edited',
        'created',
        'heard_about_from',
        'team_interest',
        'special_needs',
    ];

    protected $casts = [
        'monday' => 'boolean',
        'tuesday' => 'boolean',
        'wednesday' => 'boolean',
        'thursday' => 'boolean',
        'friday' => 'boolean',
        'substitute' => 'boolean',
        'double_position' => 'boolean',
        'previous_participation' => 'boolean',
        'team_interest' => 'boolean',
        'heard_about_from' => 'array',
        'last_edited' => 'datetime',
        'created' => 'datetime',
    ];

    protected $attributes = [
        'substitute' => false,
        'double_position' => false,
        'previous_participation' => false,
        'team_interest' => false,
        'monday' => true,
        'tuesday' => true,
        'wednesday' => true,
        'thursday' => true,
        'friday' => true,
        'special_needs' => '',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($application) {
            if (empty($application->last_edited)) {
                $application->last_edited = Carbon::now();
            }
            if (empty($application->created)) {
                $application->created = Carbon::now();
            }
        });
    }

    /**
     * Get the admission period that owns the application.
     */
    public function admissionPeriod(): BelongsTo
    {
        return $this->belongsTo(AdmissionPeriod::class, 'admission_period_id');
    }

    /**
     * Get the user that owns the application.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the interview for the application.
     */
    public function interview(): HasOne
    {
        return $this->hasOne(Interview::class, 'application_id');
    }

    /**
     * Get the potential teams for the application.
     */
    public function potentialTeams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class, 'application_team', 'application_id', 'team_id');
    }

    /**
     * Get the semester (via admission period).
     */
    public function getSemester()
    {
        return $this->admissionPeriod?->semester;
    }

    /**
     * Get the department (via admission period).
     */
    public function getDepartment()
    {
        return $this->admissionPeriod?->department;
    }
}

