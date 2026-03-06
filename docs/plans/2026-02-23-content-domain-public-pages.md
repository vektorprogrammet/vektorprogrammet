# Content Domain: Public Article Pages

> **For Claude:** REQUIRED SUB-SKILL: Use superpowers:executing-plans to implement this plan task-by-task.

**Goal:** Replace Twig article list (`/nyheter`) and article detail (`/nyhet/{slug}`) with React pages in the v2 homepage, backed by the existing `/api/articles` endpoint.

**Architecture:** v2 homepage (React Router + Vite) gets 2 new routes. The monolith API needs minor enhancements (published filter, slug filter). No auth required — articles are PUBLIC_ACCESS.

**Tech Stack:** React Router loaders, API Platform filters (monolith), daisyUI/Tailwind (v2 homepage)

---

## Context for implementer

**Monolith (minor changes):**
- Entity: `src/App/Entity/Article.php` — fields: id, title, slug, article (HTML), imageLarge, imageSmall, created, updated, sticky, published
- Endpoint: `GET /api/articles` — paginated (20/page), ordered by `created DESC`, PUBLIC_ACCESS
- Current gap: No filter for `published` status, no slug-based lookup
- Image paths: relative (e.g. `images/articles/foo.jpg`), need full URL: `{API_BASE_URL}/{path}`

**v2 homepage (new pages):**
- Working directory: `/Users/nori/Projects/ntnu/vektor/v2/homepage/`
- Branch: `monolith-merge`
- Pattern: React Router loaders + `useLoaderData` + `apiFetch` from `src/api/client.ts`
- Styling: Tailwind CSS + daisyUI components

**Twig pages being replaced:**
- `GET /nyheter` → `templates/article/index.html.twig` (paginated list, 10/page, published only)
- `GET /nyhet/{slug}` → `templates/article/show.html.twig` (single article with sidebar)

---

## Backend changes (monolith)

### Task 1: Add API filters to Article entity

Add `BooleanFilter` for `published` and `SearchFilter` for `slug` so the frontend can:
- `GET /api/articles?published=true` — only published articles
- `GET /api/articles?slug=my-article-slug` — lookup by slug

**Files:**
- Modify: `src/App/Entity/Article.php`

**Step 1: Add filter imports and attributes**

Add to Article entity:
```php
use ApiPlatform\Doctrine\Orm\Filter\BooleanFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;

// Add after #[ApiResource(...)]
#[ApiFilter(BooleanFilter::class, properties: ['published', 'sticky'])]
#[ApiFilter(SearchFilter::class, properties: ['slug' => 'exact'])]
```

**Step 2: Run tests**

```bash
php -d memory_limit=512M bin/phpunit -c phpunit.xml.dist
```

Expected: 524 tests, 0 failures.

**Step 3: Commit**

```bash
git add src/App/Entity/Article.php
git commit -m "feat: add published and slug filters to Article API"
```

---

### Task 2: Add API smoke test for article filters

**Files:**
- Modify: `tests/ApiPlatform/ArticleApiTest.php` (or create if doesn't exist)

**Step 1: Check if test file exists**

```bash
find tests/ -name "*Article*" -o -name "*article*" | head -10
```

**Step 2: Add or create test**

Test that `GET /api/articles?published=true` returns only published articles and `GET /api/articles?slug=test-slug` returns the matching article.

Use the existing `BaseWebTestCase` pattern:
```php
public function testFilterByPublished(): void
{
    $client = static::createClient();
    $client->request('GET', '/api/articles?published=true', [], [], [
        'HTTP_ACCEPT' => 'application/json',
    ]);
    $this->assertResponseIsSuccessful();
}

public function testFilterBySlug(): void
{
    $client = static::createClient();
    $client->request('GET', '/api/articles?slug=test', [], [], [
        'HTTP_ACCEPT' => 'application/json',
    ]);
    $this->assertResponseIsSuccessful();
}
```

**Step 3: Run test, commit**

```bash
php -d memory_limit=512M bin/phpunit -c phpunit.xml.dist
git add tests/
git commit -m "test: add smoke tests for Article API filters"
```

---

## Frontend changes (v2 homepage)

All remaining tasks work in `/Users/nori/Projects/ntnu/vektor/v2/homepage/` on branch `monolith-merge`.

### Task 3: Create article API module

**Files:**
- Create: `src/api/articles.ts`

**Step 1: Create the module**

```typescript
import { apiFetch } from "./client";

export interface Article {
  id: number;
  title: string;
  slug: string;
  article: string; // HTML content
  imageLarge: string | null;
  imageSmall: string | null;
  created: string; // ISO datetime
  updated: string | null;
  sticky: boolean;
  published: boolean;
}

export async function getArticles(page = 1): Promise<Array<Article>> {
  try {
    return await apiFetch<Array<Article>>(
      `/api/articles?published=true&page=${page}`
    );
  } catch (error) {
    console.error("Failed to fetch articles:", error);
    return [];
  }
}

export async function getArticleBySlug(slug: string): Promise<Article | null> {
  try {
    const articles = await apiFetch<Array<Article>>(
      `/api/articles?slug=${encodeURIComponent(slug)}&published=true`
    );
    return articles.length > 0 ? articles[0] : null;
  } catch (error) {
    console.error(`Failed to fetch article ${slug}:`, error);
    return null;
  }
}
```

**Step 2: Commit**

```bash
git add src/api/articles.ts
git commit -m "feat: add article API module"
```

---

### Task 4: Create news list page (`/nyheter`)

**Files:**
- Create: `src/routes/_home.nyheter.tsx`
- Modify: route config (if needed — check how routes are registered)

**Step 1: Check route registration pattern**

```bash
ls src/routes/_home.*.tsx
cat react-router.config.ts 2>/dev/null || cat src/routes.ts 2>/dev/null
```

Understand how routes are registered (file-based or explicit config).

**Step 2: Create the page component**

The page should:
- Fetch articles via `getArticles(page)` in a loader
- Display article cards in a grid/list (title, small image, date, excerpt)
- Show sticky articles at the top with visual distinction
- Pagination controls at the bottom
- Extract excerpt from HTML content (first ~150 chars, strip tags)
- Format dates in Norwegian locale
- Link each card to `/nyheter/{slug}`

Follow existing page patterns (e.g. `_home.foreldre.tsx`) for layout/styling.

**Step 3: Type check + commit**

```bash
npx tsc --noEmit 2>&1 | grep -v IntrinsicElements | grep "error TS" | head -10
git add src/routes/_home.nyheter.tsx
git commit -m "feat: add news list page"
```

---

### Task 5: Create article detail page (`/nyheter/:slug`)

**Files:**
- Create: `src/routes/_home.nyheter.$slug.tsx` (or equivalent for React Router nested route)

**Step 1: Create the page component**

The page should:
- Fetch article by slug via `getArticleBySlug(slug)` in a loader
- Display full article: title, large image, HTML content (via `dangerouslySetInnerHTML`), date
- Show 404-style message if article not found
- Back link to `/nyheter`
- Clean, readable article layout (max-width prose, centered)

**Step 2: Type check + commit**

```bash
npx tsc --noEmit 2>&1 | grep -v IntrinsicElements | grep "error TS" | head -10
git add src/routes/
git commit -m "feat: add article detail page"
```

---

### Task 6: Add image URL helper

Article images are stored as relative paths (e.g. `images/articles/photo.jpg`). The frontend needs to construct full URLs.

**Files:**
- Modify: `src/api/client.ts` (add helper) or `src/api/articles.ts`

**Step 1: Add helper**

```typescript
export function imageUrl(path: string | null): string | null {
  if (!path) return null;
  return `${API_BASE_URL}/${path}`;
}
```

**Step 2: Commit**

```bash
git add src/api/
git commit -m "feat: add image URL helper for article images"
```

---

### Task 7: Add Playwright e2e tests

**Files:**
- Modify: `e2e/api-integration.spec.ts`

**Step 1: Add mock article data and tests**

```typescript
const mockArticles = [
  {
    id: 1, title: "Test Article", slug: "test-article",
    article: "<p>Test content</p>", imageLarge: null, imageSmall: null,
    created: "2026-01-01T00:00:00+00:00", updated: null,
    sticky: false, published: true,
  },
];

// Add to setupApiMocks:
page.route("**/api/articles*", (route) =>
  route.fulfill({ status: 200, contentType: "application/json", body: JSON.stringify(mockArticles) }),
),

test.describe("News pages", () => {
  test("news list renders articles", async ({ page }) => { ... });
  test("article detail renders content", async ({ page }) => { ... });
  test("news list with fallback when API down", async ({ page }) => { ... });
});
```

**Step 2: Commit**

```bash
git add e2e/
git commit -m "test: add e2e tests for news list and article detail pages"
```

---

### Task 8: Final verification

**Step 1: Type check**
```bash
npx tsc --noEmit 2>&1 | grep -v IntrinsicElements | grep "error TS" | head -10
```

**Step 2: Monolith tests**
```bash
php -d memory_limit=512M bin/phpunit -c phpunit.xml.dist
```

Expected: 524+ tests, 0 failures.

**Step 3: Commit any remaining changes**
