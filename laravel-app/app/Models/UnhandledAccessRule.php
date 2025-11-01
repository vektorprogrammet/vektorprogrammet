<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * UnhandledAccessRule Model (converted from Doctrine)
 *
 * @property int $id
 * @property string $resource
 * @property string $method
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class UnhandledAccessRule extends Model
{
    use HasFactory;

    protected $table = 'unhandled_access_rule';

    protected $fillable = [
        'resource',
        'method',
    ];

    /**
     * String representation.
     */
    public function __toString(): string
    {
        return $this->method . ' ' . $this->resource;
    }
}

