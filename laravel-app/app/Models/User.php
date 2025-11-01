<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Carbon\Carbon;

/**
 * User Model (converted from Doctrine)
 *
 * @property int $id
 * @property string $last_name
 * @property string $first_name
 * @property int|null $field_of_study_id
 * @property bool $gender
 * @property string $picture_path
 * @property string $phone
 * @property string|null $account_number
 * @property string|null $user_name
 * @property string|null $password
 * @property string $email
 * @property string|null $company_email
 * @property bool $is_active
 * @property bool $reserved_from_pop_up
 * @property Carbon $last_pop_up_time
 * @property string|null $new_user_code
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class User extends Authenticatable
{
    use Notifiable;

    protected $table = 'user';

    protected $fillable = [
        'last_name',
        'first_name',
        'field_of_study_id',
        'gender',
        'picture_path',
        'phone',
        'account_number',
        'user_name',
        'password',
        'email',
        'company_email',
        'is_active',
        'reserved_from_pop_up',
        'last_pop_up_time',
        'new_user_code',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'gender' => 'boolean',
        'is_active' => 'boolean',
        'reserved_from_pop_up' => 'boolean',
        'last_pop_up_time' => 'datetime',
    ];

    protected $attributes = [
        'is_active' => true,
        'picture_path' => 'images/defaultProfile.png',
        'reserved_from_pop_up' => false,
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            if (empty($user->last_pop_up_time)) {
                $user->last_pop_up_time = Carbon::create(2000, 1, 1);
            }
        });
    }

    /**
     * Get the field of study that owns the user.
     */
    public function fieldOfStudy(): BelongsTo
    {
        return $this->belongsTo(FieldOfStudy::class, 'field_of_study_id');
    }

    /**
     * Get the roles for the user.
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'user_role', 'user_id', 'role_id');
    }

    /**
     * Get the assistant histories for the user.
     */
    public function assistantHistories(): HasMany
    {
        return $this->hasMany(AssistantHistory::class, 'user_id');
    }

    /**
     * Get the team memberships for the user.
     */
    public function teamMemberships(): HasMany
    {
        return $this->hasMany(TeamMembership::class, 'user_id');
    }

    /**
     * Get the executive board memberships for the user.
     */
    public function executiveBoardMemberships(): HasMany
    {
        return $this->hasMany(ExecutiveBoardMembership::class, 'user_id');
    }

    /**
     * Get the certificate requests for the user.
     */
    public function certificateRequests(): HasMany
    {
        return $this->hasMany(CertificateRequest::class, 'user_id');
    }

    /**
     * Get the interviews conducted by the user.
     */
    public function interviews(): HasMany
    {
        return $this->hasMany(Interview::class, 'interviewer_id');
    }

    /**
     * Get the receipts for the user.
     */
    public function receipts(): HasMany
    {
        return $this->hasMany(Receipt::class, 'user_id');
    }

    /**
     * Get the user's department (via field of study).
     */
    public function getDepartment()
    {
        return $this->fieldOfStudy?->department;
    }

    /**
     * Get the user's full name.
     */
    public function getFullName(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    /**
     * Check if user has been an assistant.
     */
    public function hasBeenAssistant(): bool
    {
        return $this->assistantHistories()->exists();
    }

    /**
     * Check if user is an active assistant.
     */
    public function isActiveAssistant(): bool
    {
        return $this->assistantHistories()
            ->whereHas('semester', function ($query) {
                $query->where('start_date', '<', now())
                      ->where('end_date', '>=', now());
            })
            ->exists();
    }

    /**
     * Get active team memberships.
     */
    public function getActiveTeamMemberships()
    {
        return $this->teamMemberships()
            ->whereNull('end_semester_id')
            ->get()
            ->toArray();
    }

    /**
     * Get active executive board memberships.
     */
    public function getActiveExecutiveBoardMemberships()
    {
        return $this->executiveBoardMemberships()
            ->where(function ($query) {
                $query->whereNull('end_semester_id')
                      ->orWhere('end_semester_id', '>', now());
            })
            ->get()
            ->toArray();
    }

    /**
     * Check if user has pending receipts.
     */
    public function hasPendingReceipts(): bool
    {
        return $this->getNumberOfPendingReceipts() !== 0;
    }

    /**
     * Get number of pending receipts.
     */
    public function getNumberOfPendingReceipts(): int
    {
        return $this->receipts()
            ->where('status', Receipt::STATUS_PENDING)
            ->count();
    }

    /**
     * Get total pending receipt sum.
     */
    public function getTotalPendingReceiptSum(): float
    {
        return $this->receipts()
            ->where('status', Receipt::STATUS_PENDING)
            ->sum('sum');
    }

    /**
     * Check if user is admin.
     */
    public function isAdmin(): bool
    {
        return $this->roles()
            ->where('role', 'ROLE_ADMIN')
            ->exists();
    }

    /**
     * Check if user is active.
     */
    public function isActive(): bool
    {
        return $this->is_active;
    }

    /**
     * String representation.
     */
    public function __toString(): string
    {
        return $this->getFullName();
    }
}
