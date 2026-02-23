# Phase 3: Wire Up Remaining Homepage Pages

> **For Claude:** REQUIRED SUB-SKILL: Use superpowers:executing-plans to implement this plan task-by-task.

**Goal:** Replace the 4 remaining hardcoded v2 homepage files (`assistenter.ts`, `faq.ts`, `foreldre.ts`, `om-oss.ts`) with API calls to the existing `/api/static_contents` endpoint.

**Architecture:** The monolith already stores all page content in the `static_content` DB table, accessible via `GET /api/static_contents` (API Platform, PUBLIC_ACCESS). Each record has `htmlId` (slug like `"assistants-header"`) and `html` (HTML string). The v2 frontend will fetch these records, filter by `htmlId` prefix, and parse the HTML into structured data. No backend changes needed.

**Tech Stack:** React Router loaders (v2 homepage), existing API Platform endpoint (monolith)

**Key insight:** `/api/static_contents` already exists and is public. The content is HTML strings, so the frontend needs a thin parsing/mapping layer.

---

## Context for implementer

**Monolith (no changes needed):**
- Entity: `src/App/Entity/StaticContent.php` — fields: `id`, `htmlId`, `html`
- Endpoint: `GET /api/static_contents` — returns all records as JSON array
- Fixtures: `src/App/DataFixtures/ORM/LoadStatic_contentData.php` — seeds ~20 records
- Content IDs follow pattern: `assistants-header`, `parent-header`, `about-header`, etc.

**v2 homepage (all changes here):**
- Shared API client: `src/api/client.ts` — `apiFetch<T>(path)` helper
- Pattern established: async fetch + hardcoded fallback (see `src/api/team.ts`, `src/api/statistics.ts`)
- Route loaders: `export async function loader()` + `useLoaderData<typeof loader>()`
- All files at `/Users/nori/Projects/ntnu/vektor/v2/homepage/`

**Static content htmlId mapping:**

| htmlId | Page | Current v2 file |
|--------|------|-----------------|
| `assistants-header` | /assistenter | `assistenter.ts` |
| `assistants-role-model` | /assistenter | `assistenter.ts` |
| `assistants-social` | /assistenter | `assistenter.ts` |
| `assistants-cv` | /assistenter | `assistenter.ts` |
| `assistants-teacher` | /assistenter | `assistenter.ts` |
| `assistants-teacher-2` | /assistenter | `assistenter.ts` |
| `assistants-tasks` | /assistenter | `assistenter.ts` |
| `assistants-admission-requirements` | /assistenter | `assistenter.ts` |
| `assistants-admission-process` | /assistenter | `assistenter.ts` |
| `about-header` | /om-oss | `om-oss.ts` |
| `about-motivate-children` | /om-oss | `om-oss.ts` |
| `about-motivate-students` | /om-oss | `om-oss.ts` |
| `about-try-teaching` | /om-oss | `om-oss.ts` |
| `about-faq` | /om-oss + /team FAQ | `faq.ts` |
| `parent-header` | /foreldre | `foreldre.ts` |
| `parent-assistants-info` | /foreldre | `foreldre.ts` |
| `parent-course` | /foreldre | `foreldre.ts` |

**API response format:** With `Accept: application/json`, API Platform returns a plain JSON array (not hydra-wrapped). The existing `apiFetch<T>` helper handles this correctly — see `sponsor.ts` for the pattern.

**Important:** The API returns raw HTML strings (with HTML entities like `&aring;`). The v2 components currently use plain text props. Two options:
- Parse HTML to extract text (strip tags) — simpler, matches current component interfaces
- Render HTML directly with `dangerouslySetInnerHTML` — preserves formatting but changes component contracts

Recommended: Use `dangerouslySetInnerHTML` since the content is trusted (admin-edited, same-origin DB). This preserves the rich formatting (lists, bold, links) that plain text would lose.

**Route pattern verified:** Currently `_home.foreldre.tsx`, `_home.om-oss.tsx`, and `_home.assistenter.tsx` call their API functions **synchronously inline** (no loader). Converting to async requires adding `export async function loader()` + `useLoaderData<typeof loader>()` — matching the existing `_home.team.tsx` pattern exactly.

---

### Task 1: Create static content API module

Create `src/api/static-content.ts` that fetches from `/api/static_contents` and provides a lookup-by-htmlId helper.

**Files:**
- Create: `src/api/static-content.ts`

**Step 1: Create the module**

```typescript
// src/api/static-content.ts
import { apiFetch } from "./client";

interface StaticContentRecord {
  id: number;
  htmlId: string;
  html: string;
}

let cachedContent: Map<string, string> | null = null;

export async function getStaticContent(): Promise<Map<string, string>> {
  if (cachedContent) return cachedContent;

  try {
    const records = await apiFetch<Array<StaticContentRecord>>("/api/static_contents");
    cachedContent = new Map(records.map((r) => [r.htmlId, r.html]));
    return cachedContent;
  } catch (error) {
    console.error("Failed to fetch static content:", error);
    return new Map();
  }
}

export async function getContentByPrefix(prefix: string): Promise<Map<string, string>> {
  const all = await getStaticContent();
  const filtered = new Map<string, string>();
  for (const [key, value] of all) {
    if (key.startsWith(prefix)) {
      filtered.set(key, value);
    }
  }
  return filtered;
}
```

**Step 2: Commit**

```bash
git add src/api/static-content.ts
git commit -m "feat: add static content API module"
```

---

### Task 2: Wire up assistenter page

Replace hardcoded `getAssistenter()` with API data from static content records with `assistants-` prefix.

**Files:**
- Modify: `src/api/assistenter.ts`
- Modify: `src/routes/_home.assistenter.tsx`

**Step 1: Rewrite assistenter.ts**

Make `getAssistenter()` async. Fetch static content records with `assistants-` prefix. Keep current hardcoded data as fallback. The function should return the same `ForAssistenterContent` interface but with `html` strings instead of plain text for card content.

Key mapping:
- `assistants-header` → title + ingress (parse `<h1>` for title, `<p>` for ingress)
- `assistants-role-model` → card 1
- `assistants-social` → card 2
- `assistants-cv` → card 3

**Step 2: Add loader to route**

Add `export async function loader()` to `_home.assistenter.tsx` that calls the async `getAssistenter()`. Use `useLoaderData` in the component.

**Step 3: Run `npx tsc --noEmit` to check types compile**

Expected: only pre-existing JSX type errors, no new errors.

**Step 4: Commit**

```bash
git add src/api/assistenter.ts src/routes/_home.assistenter.tsx
git commit -m "feat: wire up assistenter page to static content API"
```

---

### Task 3: Wire up foreldre page

Replace hardcoded `getForeldre()` with API data from `parent-` prefix records.

**Files:**
- Modify: `src/api/foreldre.ts`
- Modify: `src/routes/_home.foreldre.tsx`

**Step 1: Rewrite foreldre.ts**

Make `getForeldre()` async. Fetch records with `parent-` prefix. Map:
- `parent-header` → title + ingress
- `parent-assistants-info` → card 1
- `parent-course` → card 2 + bottomText

**Step 2: Add loader to route**

Same pattern as Task 2.

**Step 3: Type check + commit**

```bash
git add src/api/foreldre.ts src/routes/_home.foreldre.tsx
git commit -m "feat: wire up foreldre page to static content API"
```

---

### Task 4: Wire up om-oss page

Replace hardcoded `getOmOss()` with API data from `about-` prefix records.

**Files:**
- Modify: `src/api/om-oss.ts`
- Modify: `src/routes/_home.om-oss.tsx`

**Step 1: Rewrite om-oss.ts**

Make `getOmOss()` async. Fetch records with `about-` prefix. Map:
- `about-header` → title + ingress
- `about-motivate-children` → card 1
- `about-motivate-students` → card 2
- `about-try-teaching` → bottomHeader + bottomText

**Step 2: Add loader to route**

Same pattern as Task 2.

**Step 3: Type check + commit**

```bash
git add src/api/om-oss.ts src/routes/_home.om-oss.tsx
git commit -m "feat: wire up om-oss page to static content API"
```

---

### Task 5: Wire up FAQ

Replace hardcoded `getAssistantFaqs()` and `getTeamFaqs()` with API data from the `about-faq` record.

**Files:**
- Modify: `src/api/faq.ts`
- Modify: `src/routes/_home.assistenter.tsx` (already has loader from Task 2)
- Modify: `src/routes/_home.team.tsx` (already has loader)

**Step 1: Rewrite faq.ts**

The `about-faq` record contains all FAQ as HTML (`<h5>question</h5><p>answer</p>` pairs). Parse the HTML to extract Q&A pairs.

Make both functions async:
- `getAssistantFaqs()` — returns first 9 FAQ pairs (assistant-related)
- `getTeamFaqs()` — returns last 4 FAQ pairs (team-related)

Keep hardcoded fallback data.

**Step 2: Update loaders**

The routes that use FAQ already have loaders (from Tasks 2 and Phase 2C). Add FAQ fetch to existing loaders.

**Step 3: Type check + commit**

```bash
git add src/api/faq.ts src/routes/_home.assistenter.tsx src/routes/_home.team.tsx
git commit -m "feat: wire up FAQ to static content API"
```

---

### Task 6: Add Playwright tests for new pages

Add e2e tests verifying the newly wired pages render correctly with mocked API responses.

**Files:**
- Modify: `e2e/api-integration.spec.ts`

**Step 1: Add test cases**

```typescript
test.describe("Assistenter page from API", () => {
  test("renders assistant content from static content API", async ({ page }) => {
    // Mock /api/static_contents to return test content
    // Navigate to /assistenter
    // Verify heading and card content render
  });
});

test.describe("Foreldre page from API", () => { ... });
test.describe("Om oss page from API", () => { ... });
```

**Step 2: Commit**

```bash
git add e2e/api-integration.spec.ts
git commit -m "test: add e2e tests for assistenter, foreldre, om-oss pages"
```

---

### Task 7: Final verification + commit

**Step 1: Run type check**

```bash
npx tsc --noEmit 2>&1 | grep -v IntrinsicElements | grep "error TS"
```

Expected: no new errors.

**Step 2: Verify all API files use async + fallback pattern**

```bash
grep -l "apiFetch\|getStaticContent" src/api/*.ts
```

Should list: `client.ts`, `assistenter.ts`, `foreldre.ts`, `om-oss.ts`, `faq.ts`, `static-content.ts`, `team.ts`, `kontakt.ts`, `statistics.ts`, `sponsor.ts`, `contact.ts`, `departments.ts`

**Step 3: Commit docs update**

Update `src/api/` README or equivalent if one exists.

```bash
git add -A
git commit -m "chore: Phase 3 complete — all homepage pages wired to API"
```
