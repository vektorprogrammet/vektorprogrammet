<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Carbon\Carbon;

/**
 * Article Model (converted from Doctrine)
 *
 * @property int $id
 * @property string $title
 * @property string $slug
 * @property string $article
 * @property string $image_large
 * @property string $image_small
 * @property Carbon $created
 * @property Carbon $updated
 * @property bool $sticky
 * @property bool|null $published
 * @property int|null $author_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class Article extends Model
{
    use HasFactory;

    protected $table = 'article';

    protected $fillable = [
        'title',
        'slug',
        'article',
        'image_large',
        'image_small',
        'created',
        'updated',
        'sticky',
        'published',
        'author_id',
    ];

    protected $casts = [
        'sticky' => 'boolean',
        'published' => 'boolean',
        'created' => 'datetime',
        'updated' => 'datetime',
    ];

    protected $attributes = [
        'published' => false,
    ];

    /**
     * Boot the model.
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($article): void {
            if (empty($article->created)) {
                $article->created = Carbon::now();
            }
            if (empty($article->updated)) {
                $article->updated = Carbon::now();
            }
        });

        static::updating(function ($article): void {
            $article->updated = Carbon::now();
        });
    }

    /**
     * Get the author that owns the article.
     *
     * @return BelongsTo
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /**
     * Get the departments for the article.
     *
     * @return BelongsToMany
     */
    public function departments(): BelongsToMany
    {
        return $this->belongsToMany(
            Department::class,
            'articles_departments',
            'article_id',
            'department_id'
        );
    }

    /**
     * Check if article is published.
     */
    public function isPublished(): bool
    {
        return (bool) $this->published;
    }

    /**
     * String representation.
     */
    public function __toString(): string
    {
        return $this->title ?? '';
    }
}

