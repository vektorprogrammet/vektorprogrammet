<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

/**
 * ChangeLogItem Model (converted from Doctrine)
 *
 * @property int $id
 * @property string $title
 * @property string|null $description
 * @property string $github_link
 * @property Carbon $date
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class ChangeLogItem extends Model
{
    use HasFactory;

    protected $table = 'change_log_item';

    protected $fillable = [
        'title',
        'description',
        'github_link',
        'date',
    ];

    protected $casts = [
        'date' => 'datetime',
    ];
}

