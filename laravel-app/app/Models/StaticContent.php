<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * StaticContent Model (converted from Doctrine)
 *
 * @property int $id
 * @property string $html_id
 * @property string $html
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class StaticContent extends Model
{
    use HasFactory;

    protected $table = 'static_content';

    protected $fillable = [
        'html_id',
        'html',
    ];

    /**
     * String representation.
     */
    public function __toString(): string
    {
        return $this->html_id ?? '';
    }
}

