<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

/**
 * Receipt Model (converted from Doctrine)
 *
 * @property int $id
 * @property int $user_id
 * @property Carbon|null $submit_date
 * @property Carbon $receipt_date
 * @property Carbon|null $refund_date
 * @property string|null $picture_path
 * @property string $description
 * @property float $sum
 * @property string $status
 * @property string|null $visual_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class Receipt extends Model
{
    use HasFactory;

    const STATUS_PENDING = 'pending';
    const STATUS_REFUNDED = 'refunded';
    const STATUS_REJECTED = 'rejected';

    protected $table = 'receipt';

    protected $fillable = [
        'user_id',
        'submit_date',
        'receipt_date',
        'refund_date',
        'picture_path',
        'description',
        'sum',
        'status',
        'visual_id',
    ];

    protected $casts = [
        'submit_date' => 'datetime',
        'receipt_date' => 'datetime',
        'refund_date' => 'datetime',
        'sum' => 'float',
    ];

    protected $attributes = [
        'status' => self::STATUS_PENDING,
    ];

    /**
     * Boot the model.
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($receipt): void {
            if (empty($receipt->submit_date)) {
                $receipt->submit_date = Carbon::now();
            }
            if (empty($receipt->receipt_date)) {
                $receipt->receipt_date = Carbon::now();
            }
            if (empty($receipt->visual_id)) {
                $currentTimeInMilliseconds = round(microtime(true) * 1000);
                $receipt->visual_id = dechex($currentTimeInMilliseconds);
            }
        });
    }

    /**
     * Get the user that owns the receipt.
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * String representation.
     */
    public function __toString(): string
    {
        return $this->visual_id ?? '';
    }
}

