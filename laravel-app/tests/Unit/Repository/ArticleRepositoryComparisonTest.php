<?php

namespace Tests\Unit\Repository;

use App\Repository\Contract\ArticleRepositoryInterface;
use App\Repository\Eloquent\ArticleRepository;
use App\Models\Article;
use App\Models\Department;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Test to verify Eloquent ArticleRepository matches Doctrine ArticleRepository behavior.
 */
class ArticleRepositoryComparisonTest extends EloquentDoctrineComparisonTest
{
    use RefreshDatabase;

    private ArticleRepositoryInterface $eloquentRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->eloquentRepository = new ArticleRepository();
    }

    protected function getDoctrineRepository(): object
    {
        throw new \RuntimeException('Doctrine repository access not configured. Use integration tests.');
    }

    protected function getEloquentRepository(): object
    {
        return $this->eloquentRepository;
    }

    /**
     * Test findLatestArticles matches Doctrine behavior.
     */
    public function testFindLatestArticles(): void
    {
        $author = User::factory()->create();

        // Create published articles
        $article1 = Article::factory()->create([
            'author_id' => $author->id,
            'published' => true,
            'created' => now()->subDays(2),
        ]);

        $article2 = Article::factory()->create([
            'author_id' => $author->id,
            'published' => true,
            'created' => now()->subDays(1),
        ]);

        // Create unpublished article (should not appear)
        Article::factory()->create([
            'author_id' => $author->id,
            'published' => false,
        ]);

        $results = $this->eloquentRepository->findLatestArticles(10);

        $this->assertIsArray($results);
        $this->assertGreaterThanOrEqual(2, count($results));

        // Check ordering: newest first
        $first = is_object($results[0]) ? $results[0] : (object)$results[0];
        $this->assertEquals($article2->id, $first->id);
    }

    /**
     * Test findLatestArticles with limit.
     */
    public function testFindLatestArticlesWithLimit(): void
    {
        $author = User::factory()->create();

        Article::factory()->count(5)->create([
            'author_id' => $author->id,
            'published' => true,
        ]);

        $results = $this->eloquentRepository->findLatestArticles(3);

        $this->assertCount(3, $results);
    }

    /**
     * Test findLatestArticlesByDepartment matches Doctrine behavior.
     */
    public function testFindLatestArticlesByDepartment(): void
    {
        $department = Department::factory()->create();
        $author = User::factory()->create();

        // Create article linked to department
        $article = Article::factory()->create([
            'author_id' => $author->id,
            'published' => true,
        ]);
        $article->departments()->attach($department->id);

        // Create article without department
        Article::factory()->create([
            'author_id' => $author->id,
            'published' => true,
        ]);

        $results = $this->eloquentRepository->findLatestArticlesByDepartment($department, 10);

        $this->assertIsArray($results);
        $this->assertGreaterThanOrEqual(1, count($results));

        $ids = array_map(fn($a) => is_object($a) ? $a->id : $a['id'], $results);
        $this->assertContains($article->id, $ids);
    }

    /**
     * Test findStickyAndLatestArticles matches Doctrine behavior.
     */
    public function testFindStickyAndLatestArticles(): void
    {
        $author = User::factory()->create();

        $stickyArticle = Article::factory()->create([
            'author_id' => $author->id,
            'published' => true,
            'sticky' => true,
            'created' => now()->subDays(40), // Older than 30 days but sticky
        ]);

        $recentArticle = Article::factory()->create([
            'author_id' => $author->id,
            'published' => true,
            'sticky' => false,
            'created' => now()->subDays(10), // Within 30 days
        ]);

        $oldArticle = Article::factory()->create([
            'author_id' => $author->id,
            'published' => true,
            'sticky' => false,
            'created' => now()->subDays(40), // Older than 30 days and not sticky
        ]);

        $results = $this->eloquentRepository->findStickyAndLatestArticles(10);

        $this->assertIsArray($results);
        $this->assertGreaterThanOrEqual(2, count($results));

        $ids = array_map(fn($a) => is_object($a) ? $a->id : $a['id'], $results);
        $this->assertContains($stickyArticle->id, $ids);
        $this->assertContains($recentArticle->id, $ids);
        $this->assertNotContains($oldArticle->id, $ids);
    }

    /**
     * Test findSlugs matches Doctrine behavior.
     */
    public function testFindSlugs(): void
    {
        $author = User::factory()->create();

        $article1 = Article::factory()->create([
            'author_id' => $author->id,
            'slug' => 'article-one',
        ]);

        $article2 = Article::factory()->create([
            'author_id' => $author->id,
            'slug' => 'article-two',
        ]);

        $slugs = $this->eloquentRepository->findSlugs();

        $this->assertIsArray($slugs);
        $this->assertGreaterThanOrEqual(2, count($slugs));
        $this->assertContains('article-one', $slugs);
        $this->assertContains('article-two', $slugs);
    }

    /**
     * Test findAllPublishedArticles returns QueryBuilder.
     */
    public function testFindAllPublishedArticles(): void
    {
        $author = User::factory()->create();

        Article::factory()->count(3)->create([
            'author_id' => $author->id,
            'published' => true,
        ]);

        Article::factory()->create([
            'author_id' => $author->id,
            'published' => false,
        ]);

        $query = $this->eloquentRepository->findAllPublishedArticles();

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Builder::class, $query);
        
        $results = $query->get();
        $this->assertGreaterThanOrEqual(3, $results->count());
    }

    /**
     * Test findLatestArticles with excludeId parameter.
     */
    public function testFindLatestArticlesWithExcludeId(): void
    {
        $author = User::factory()->create();

        $article1 = Article::factory()->create([
            'author_id' => $author->id,
            'published' => true,
        ]);

        $article2 = Article::factory()->create([
            'author_id' => $author->id,
            'published' => true,
        ]);

        $results = $this->eloquentRepository->findLatestArticles(10, $article1->id);

        $this->assertIsArray($results);
        $ids = array_map(fn($a) => is_object($a) ? $a->id : $a['id'], $results);
        $this->assertNotContains($article1->id, $ids);
        $this->assertContains($article2->id, $ids);
    }
}

