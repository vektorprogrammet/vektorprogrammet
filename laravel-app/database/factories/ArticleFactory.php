<?php

namespace Database\Factories;

use App\Models\Article;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Article>
 */
class ArticleFactory extends Factory
{
    protected $model = Article::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(),
            'slug' => $this->faker->unique()->slug(),
            'article' => $this->faker->paragraphs(3, true),
            'author_id' => User::factory(),
            'published' => true,
            'sticky' => false,
            'created' => now(),
        ];
    }

    /**
     * Indicate that the article is sticky.
     */
    public function sticky(): static
    {
        return $this->state(fn (array $attributes) => [
            'sticky' => true,
        ]);
    }

    /**
     * Indicate that the article is unpublished.
     */
    public function unpublished(): static
    {
        return $this->state(fn (array $attributes) => [
            'published' => false,
        ]);
    }
}

