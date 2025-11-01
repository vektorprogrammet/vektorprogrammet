<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

/**
 * InfoMeeting Model (converted from Doctrine)
 *
 * @property int $id
 * @property bool|null $show_on_page
 * @property Carbon|null $date
 * @property string|null $room
 * @property string|null $description
 * @property string|null $link
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class InfoMeeting extends Model
{
    use HasFactory;

    protected $table = 'infomeeting';

    protected $fillable = [
        'show_on_page',
        'date',
        'room',
        'description',
        'link',
    ];

    protected $casts = [
        'show_on_page' => 'boolean',
        'date' => 'datetime',
    ];

    /**
     * Set link with automatic http:// prefix.
     */
    public function setLink(?string $link): void
    {
        if ($link !== null && strlen($link) > 0 && substr($link, 0, 4) !== 'http') {
            $link = "http://{$link}";
        }

        $this->link = $link;
    }

    /**
     * String representation.
     */
    public function __toString(): string
    {
        return "Infomøte";
    }
}

