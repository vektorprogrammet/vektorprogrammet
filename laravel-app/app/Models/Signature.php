<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Signature Model (converted from Doctrine)
 *
 * @property int $id
 * @property string|null $signature_path
 * @property string $description
 * @property string|null $additional_comment
 * @property int $user_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class Signature extends Model
{
    use HasFactory;

    protected $table = 'signature';

    protected $fillable = [
        'signature_path',
        'description',
        'additional_comment',
        'user_id',
    ];

    /**
     * Get the user that owns the signature.
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get additional comment or null.
     */
    public function getAdditionalComment(): ?string
    {
        return $this->additional_comment;
    }
}

