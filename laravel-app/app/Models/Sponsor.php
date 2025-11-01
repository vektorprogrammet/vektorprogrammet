<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Sponsor Model (converted from Doctrine)
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $url
 * @property string|null $size
 * @property string|null $logo_image_path
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class Sponsor extends Model
{
    use HasFactory;

    protected $table = 'sponsor';

    protected $fillable = [
        'name',
        'url',
        'size',
        'logo_image_path',
    ];

    protected $attributes = [
        'size' => 'medium',
    ];
}

