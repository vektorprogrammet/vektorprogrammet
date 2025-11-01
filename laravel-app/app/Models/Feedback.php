<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

/**
 * Feedback Model (converted from Doctrine)
 *
 * @property int $id
 * @property string $title
 * @property string $description
 * @property string $type
 * @property int $user_id
 * @property Carbon $created_at
 * @property Carbon|null $updated_at
 */
class Feedback extends Model
{
    use HasFactory;

    const TYPE_QUESTION = 'question';
    const TYPE_ERROR = 'error';
    const TYPE_FEATURE_REQUEST = 'feature_request';

    protected $table = 'feedback';

    public $timestamps = true;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $fillable = [
        'title',
        'description',
        'type',
        'user_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    /**
     * Get the user that owns the feedback.
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get type string in Norwegian locale.
     */
    public function getTypeString(): string
    {
        return match ($this->type) {
            self::TYPE_ERROR => "Feil",
            self::TYPE_QUESTION => "Spørsmål",
            self::TYPE_FEATURE_REQUEST => "Ny funksjonalitet",
            default => "",
        };
    }

    /**
     * Get Slack message body.
     */
    public function getSlackMessageBody(): string
    {
        $usr = "";
        if ($this->user) {
            $usr .= "{$this->user->getFullName()}";
        }
        $returnString =
            "Feedback\n" .
            "Fra *{$usr}*\n" .
            "*{$this->getTypeString()}*: `{$this->title}`\n" .
            "```{$this->description}```\n";

        return $returnString;
    }
}

