<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

/**
 * PasswordReset Model (converted from Doctrine)
 *
 * @property int $id
 * @property int $user_id
 * @property string $hashed_reset_code
 * @property Carbon $reset_time
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class PasswordReset extends Model
{
    use HasFactory;

    protected $table = 'password_reset';

    protected $fillable = [
        'user_id',
        'hashed_reset_code',
        'reset_time',
    ];

    protected $casts = [
        'reset_time' => 'datetime',
    ];

    /**
     * Boot the model.
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($passwordReset): void {
            if (empty($passwordReset->reset_time)) {
                $passwordReset->reset_time = Carbon::now();
            }
        });
    }

    /**
     * Get the user that owns the password reset.
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

