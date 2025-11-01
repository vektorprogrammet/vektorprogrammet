<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * CertificateRequest Model (converted from Doctrine)
 *
 * @property int $id
 * @property int $user_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class CertificateRequest extends Model
{
    use HasFactory;

    protected $table = 'certificate_request';

    protected $fillable = [
        'user_id',
    ];

    /**
     * Get the user that owns the certificate request.
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

