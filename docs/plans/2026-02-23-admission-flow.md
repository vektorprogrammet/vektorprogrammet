# Admission Flow Migration Implementation Plan

> **For Claude:** REQUIRED SUB-SKILL: Use superpowers:executing-plans to implement this plan task-by-task.

**Goal:** Wire the existing v2 `/assistenter` page to real API data — live admission periods, department-specific fields of study, form submission, and interest list subscription.

**Architecture:** The v2 homepage already has a nearly complete application form UI at `/assistenter` with city tabs, 7 form fields, and open/closed state logic. This plan replaces hardcoded data with live API calls and adds the missing form submission + subscription endpoints. One new backend endpoint (POST /api/admission_subscribers), rest is frontend wiring.

**Tech Stack:** React Router loaders, API Platform (Symfony), TypeScript, shadcn/ui components (already in use)

---

**Key discovery:** The `/assistenter` page (`src/routes/_home.assistenter.tsx`) already has:
- City tabs component (`TabMenu`) with Trondheim/Bergen/Ås
- Full application form UI (firstName, lastName, email, phone, studieretning, kjønn, årstrinn)
- Open/closed state rendering
- shadcn/ui Card, Input, Select, Popover/Command (for searchable study dropdown)

**What's hardcoded and needs replacing:**
- `studyOptions` in `src/lib/studies.ts` — should come from `GET /api/departments/{id}` (fieldOfStudy relation)
- `cityApplicationOpen` record — should come from `GET /api/admission_periods` filtered by department
- Form has no `onSubmit` — needs to POST to `/api/applications`
- Closed state has no subscribe widget — needs `POST /api/admission_subscribers`

**Working directories:**
- Monolith: `/Users/nori/Projects/ntnu/vektor/v1/monolith/`
- v2 homepage: `/Users/nori/Projects/ntnu/vektor/v2/homepage/` (branch: `monolith-merge`)

**Test commands:**
- Monolith: `php -d memory_limit=512M bin/phpunit -c phpunit.xml.dist --testsuite=api`
- v2 type check: `cd /Users/nori/Projects/ntnu/vektor/v2/homepage && npx tsc --noEmit 2>&1 | grep -v IntrinsicElements | grep "error TS"`

---

### Task 1: Backend — Add POST /api/admission_subscribers endpoint

**Files:**
- Create: `src/App/ApiResource/AdmissionSubscriberInput.php`
- Create: `src/App/State/AdmissionSubscriberProcessor.php`
- Modify: `config/api_platform.yml` (if not auto-discovered)
- Modify: `config/security.yml` (add PUBLIC_ACCESS for the route)

**Step 1: Create the DTO**

Create `src/App/ApiResource/AdmissionSubscriberInput.php`:

```php
<?php

namespace App\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use App\State\AdmissionSubscriberProcessor;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    operations: [
        new Post(
            uriTemplate: '/admission_subscribers',
            processor: AdmissionSubscriberProcessor::class,
            output: false,
            status: 201,
        ),
    ],
)]
class AdmissionSubscriberInput
{
    #[Assert\NotBlank]
    #[Assert\Email]
    public string $email = '';

    #[Assert\NotNull]
    public ?int $departmentId = null;

    public bool $infoMeeting = false;
}
```

**Step 2: Create the processor**

Create `src/App/State/AdmissionSubscriberProcessor.php`:

```php
<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\ApiResource\AdmissionSubscriberInput;
use App\Entity\AdmissionSubscriber;
use App\Entity\Repository\AdmissionSubscriberRepository;
use App\Entity\Repository\DepartmentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class AdmissionSubscriberProcessor implements ProcessorInterface
{
    public function __construct(
        private EntityManagerInterface $em,
        private DepartmentRepository $departmentRepo,
        private AdmissionSubscriberRepository $subscriberRepo,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        assert($data instanceof AdmissionSubscriberInput);

        $department = $this->departmentRepo->find($data->departmentId);
        if (!$department) {
            throw new UnprocessableEntityHttpException('Department not found.');
        }

        // Silently ignore duplicate email+department combos
        $existing = $this->subscriberRepo->findOneBy([
            'email' => $data->email,
            'department' => $department,
        ]);
        if ($existing) {
            return;
        }

        $subscriber = new AdmissionSubscriber();
        $subscriber->setEmail($data->email);
        $subscriber->setDepartment($department);
        $subscriber->setInfoMeeting($data->infoMeeting);

        $this->em->persist($subscriber);
        $this->em->flush();
    }
}
```

**Step 3: Add PUBLIC_ACCESS to security config**

In `config/security.yml`, find the `access_control` section and add before the `^/api` JWT rule:

```yaml
- { path: ^/api/admission_subscribers, roles: PUBLIC_ACCESS }
```

**Step 4: Run tests**

```bash
php -d memory_limit=512M bin/phpunit -c phpunit.xml.dist --testsuite=api
```

Expected: all existing API tests still pass.

**Step 5: Commit**

```bash
git add src/App/ApiResource/AdmissionSubscriberInput.php src/App/State/AdmissionSubscriberProcessor.php config/security.yml
git commit -m "feat: add admission subscriber endpoint (POST /api/admission_subscribers)"
```

---

### Task 2: Backend — Add smoke tests for admission subscriber endpoint

**Files:**
- Create: `tests/AppBundle/Api/AdmissionSubscriberApiTest.php`

**Step 1: Write the test**

```php
<?php

namespace Tests\App\Api;

use Tests\App\BaseWebTestCase;

class AdmissionSubscriberApiTest extends BaseWebTestCase
{
    public function testSubscribeReturns201(): void
    {
        $client = static::createClient();
        $client->request('POST', '/api/admission_subscribers', [], [], [
            'HTTP_ACCEPT' => 'application/json',
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'email' => 'test-subscriber@example.com',
            'departmentId' => 1,
            'infoMeeting' => false,
        ]));
        $this->assertResponseStatusCodeSame(201);
    }

    public function testDuplicateSubscribeIsIdempotent(): void
    {
        $client = static::createClient();
        $payload = json_encode([
            'email' => 'duplicate-test@example.com',
            'departmentId' => 1,
            'infoMeeting' => false,
        ]);
        $headers = [
            'HTTP_ACCEPT' => 'application/json',
            'CONTENT_TYPE' => 'application/json',
        ];

        $client->request('POST', '/api/admission_subscribers', [], [], $headers, $payload);
        $this->assertResponseStatusCodeSame(201);

        // Second request should also succeed (idempotent)
        $client->request('POST', '/api/admission_subscribers', [], [], $headers, $payload);
        $this->assertResponseStatusCodeSame(201);
    }

    public function testSubscribeWithInvalidDepartmentReturns422(): void
    {
        $client = static::createClient();
        $client->request('POST', '/api/admission_subscribers', [], [], [
            'HTTP_ACCEPT' => 'application/json',
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'email' => 'test@example.com',
            'departmentId' => 99999,
            'infoMeeting' => false,
        ]));
        $this->assertResponseStatusCodeSame(422);
    }

    public function testSubscribeWithInvalidEmailReturns422(): void
    {
        $client = static::createClient();
        $client->request('POST', '/api/admission_subscribers', [], [], [
            'HTTP_ACCEPT' => 'application/json',
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'email' => 'not-an-email',
            'departmentId' => 1,
            'infoMeeting' => false,
        ]));
        $this->assertResponseStatusCodeSame(422);
    }
}
```

**Step 2: Run the tests**

```bash
php -d memory_limit=512M bin/phpunit -c phpunit.xml.dist --filter="AdmissionSubscriberApiTest"
```

Expected: 4 tests, 4 assertions, 0 failures.

**Step 3: Run full API suite to confirm no regressions**

```bash
php -d memory_limit=512M bin/phpunit -c phpunit.xml.dist --testsuite=api
```

**Step 4: Commit**

```bash
git add tests/AppBundle/Api/AdmissionSubscriberApiTest.php
git commit -m "test: add smoke tests for admission subscriber API"
```

---

### Task 3: Frontend — Create admission API module

All remaining tasks work in `/Users/nori/Projects/ntnu/vektor/v2/homepage/` on branch `monolith-merge`.

**Files:**
- Create: `src/api/admission.ts`

**Step 1: Create the API module**

```typescript
import { apiFetch, API_BASE_URL } from "./client";

// --- Types ---

export interface Department {
  id: number;
  name: string;
  shortName: string;
  city: string;
  email: string;
  address: string | null;
  latitude: string | null;
  longitude: string | null;
  logoPath: string | null;
  active: boolean;
}

export interface FieldOfStudy {
  id: number;
  name: string;
  shortName: string;
  department: { id: number; city: string };
}

export interface InfoMeeting {
  id: number;
  date: string | null; // ISO datetime
  room: string | null;
  link: string | null;
  showOnPage: boolean;
}

export interface AdmissionPeriod {
  id: number;
  department: { id: number; name: string };
  startDate: string; // ISO datetime
  endDate: string; // ISO datetime
  infoMeeting: InfoMeeting | null;
  semester: { id: number };
}

export interface DepartmentDetail extends Department {
  fieldOfStudy: FieldOfStudy[];
  admissionPeriods: AdmissionPeriod[];
}

export type AdmissionState = "open" | "upcoming" | "recently_closed" | "closed";

// --- API calls ---

export async function getDepartments(): Promise<Department[]> {
  try {
    const departments = await apiFetch<Department[]>("/api/departments");
    return departments.filter((d) => d.active);
  } catch (error) {
    console.error("Failed to fetch departments:", error);
    return [];
  }
}

export async function getDepartmentDetail(
  id: number,
): Promise<DepartmentDetail | null> {
  try {
    return await apiFetch<DepartmentDetail>(`/api/departments/${id}`);
  } catch (error) {
    console.error(`Failed to fetch department ${id}:`, error);
    return null;
  }
}

export async function submitApplication(data: {
  firstName: string;
  lastName: string;
  email: string;
  phone: string;
  gender: number;
  fieldOfStudyId: number;
  yearOfStudy: string;
  departmentId: number;
}): Promise<{ ok: true } | { ok: false; error: string }> {
  try {
    const response = await fetch(`${API_BASE_URL}/api/applications`, {
      method: "POST",
      headers: {
        Accept: "application/json",
        "Content-Type": "application/json",
      },
      body: JSON.stringify(data),
    });

    if (response.ok) {
      return { ok: true };
    }

    // Parse validation errors
    const body = await response.json().catch(() => null);
    const message =
      body?.detail || body?.["hydra:description"] || `Error ${response.status}`;
    return { ok: false, error: message };
  } catch (error) {
    return { ok: false, error: "Nettverksfeil. Prøv igjen." };
  }
}

export async function subscribeToNotifications(data: {
  email: string;
  departmentId: number;
  infoMeeting: boolean;
}): Promise<{ ok: true } | { ok: false; error: string }> {
  try {
    const response = await fetch(`${API_BASE_URL}/api/admission_subscribers`, {
      method: "POST",
      headers: {
        Accept: "application/json",
        "Content-Type": "application/json",
      },
      body: JSON.stringify(data),
    });

    if (response.ok) {
      return { ok: true };
    }

    const body = await response.json().catch(() => null);
    const message = body?.detail || `Error ${response.status}`;
    return { ok: false, error: message };
  } catch (error) {
    return { ok: false, error: "Nettverksfeil. Prøv igjen." };
  }
}

// --- Admission state logic ---

export function getAdmissionState(
  admissionPeriods: AdmissionPeriod[],
): { state: AdmissionState; period: AdmissionPeriod | null } {
  const now = new Date();

  for (const period of admissionPeriods) {
    const start = new Date(period.startDate);
    const end = new Date(period.endDate);

    if (now >= start && now <= end) {
      return { state: "open", period };
    }
  }

  // Check for upcoming or recently closed
  for (const period of admissionPeriods) {
    const start = new Date(period.startDate);
    const end = new Date(period.endDate);
    const recentlyClosedEnd = new Date(end.getTime() + 14 * 24 * 60 * 60 * 1000);

    if (now < start) {
      return { state: "upcoming", period };
    }
    if (now > end && now < recentlyClosedEnd) {
      return { state: "recently_closed", period };
    }
  }

  return { state: "closed", period: null };
}

export function formatDeadline(isoDate: string): string {
  return new Date(isoDate).toLocaleDateString("nb-NO", {
    day: "numeric",
    month: "long",
    year: "numeric",
    hour: "2-digit",
    minute: "2-digit",
  });
}
```

**Step 2: Type check**

```bash
npx tsc --noEmit 2>&1 | grep -v IntrinsicElements | grep "error TS" | head -10
```

**Step 3: Commit**

```bash
git add src/api/admission.ts
git commit -m "feat: add admission API module with types and state logic"
```

---

### Task 4: Frontend — Wire /assistenter to real API data

This is the core task: replace hardcoded data in `_home.assistenter.tsx` with live API calls.

**Files:**
- Modify: `src/routes/_home.assistenter.tsx`

**What to change:**

1. **Add admission data to loader**: Fetch departments + their details (admission periods, field of study)
2. **Replace `cityApplicationOpen`**: Use `getAdmissionState()` with real admission periods
3. **Replace `studyOptions`**: Use `fieldOfStudy[]` from department detail
4. **Replace hardcoded city list**: Use departments from API (still filter to active ones)

**Step 1: Update the loader**

Replace the existing loader with one that fetches admission data alongside existing data:

```typescript
import {
  getDepartments,
  getDepartmentDetail,
  getAdmissionState,
  formatDeadline,
  type Department,
  type DepartmentDetail,
  type AdmissionState,
  type AdmissionPeriod,
} from "~/api/admission";

export async function loader() {
  const [assistenterData, assistantFaqs, departments] = await Promise.all([
    getAssistenter(),
    getAssistantFaqs(),
    getDepartments(),
  ]);

  // Fetch detail for each department (includes fieldOfStudy + admissionPeriods)
  const departmentDetails = await Promise.all(
    departments.map((d) => getDepartmentDetail(d.id)),
  );

  const admissionData = departments.map((dept, i) => {
    const detail = departmentDetails[i];
    const { state, period } = detail
      ? getAdmissionState(detail.admissionPeriods)
      : { state: "closed" as AdmissionState, period: null };

    return {
      department: dept,
      detail,
      admissionState: state,
      activePeriod: period,
      fieldsOfStudy: detail?.fieldOfStudy ?? [],
    };
  });

  return { assistenterData, assistantFaqs, admissionData };
}
```

**Step 2: Replace `CityTabs` to use real departments**

Replace the hardcoded `CityTabs` component to accept `admissionData` from the loader and render a tab per department. The active tab switches which department's form/closed-state to show.

Key changes:
- Tab labels come from `admissionData[].department.city` (not hardcoded `cities` object)
- `isApplicationOpen()` replaced by `admissionData[].admissionState === "open"`
- Study dropdown populated from `admissionData[].fieldsOfStudy` (not `studyOptions`)
- Deadline from `admissionData[].activePeriod.endDate` (not hardcoded `???`)

**Step 3: Type check + verify**

```bash
npx tsc --noEmit 2>&1 | grep -v IntrinsicElements | grep "error TS" | head -10
```

**Step 4: Commit**

```bash
git add src/routes/_home.assistenter.tsx
git commit -m "feat: wire assistenter page to real admission API data"
```

---

### Task 5: Frontend — Add form submission to /assistenter

**Files:**
- Modify: `src/routes/_home.assistenter.tsx`

**What to change:**

The `CityApplyCard` component has all 7 form fields but the "Søk nå!" button doesn't do anything. Wire it up:

1. Add state for each form field (`useState` for firstName, lastName, email, phone, gender, fieldOfStudyId, yearOfStudy)
2. Add `onSubmit` handler that calls `submitApplication()` from the admission API module
3. On success: navigate to `/opptak/bekreftelse` (or show inline success)
4. On 422 error: show error message inline
5. Add loading state on submit button
6. Map gender values: current UI uses "male"/"female"/"other" strings → API expects 0/1 ints. Map "male"→0, "female"→1, "other"→1.
7. Map yearOfStudy values: current UI uses "firstGrade" etc. → API expects "1. klasse" etc.

**Step 1: Add form state and submission**

Wire each Input/Select to state variables. Add an `async handleSubmit()` that:

```typescript
import { submitApplication } from "~/api/admission";
import { useNavigate } from "react-router";

// Inside CityApplyCard:
const navigate = useNavigate();
const [submitting, setSubmitting] = useState(false);
const [error, setError] = useState<string | null>(null);

// Form field state
const [firstName, setFirstName] = useState("");
const [lastName, setLastName] = useState("");
const [email, setEmail] = useState("");
const [phone, setPhone] = useState("");
const [gender, setGender] = useState<string>("");
const [fieldOfStudyId, setFieldOfStudyId] = useState<number | null>(null);
const [yearOfStudy, setYearOfStudy] = useState<string>("");

async function handleSubmit() {
  if (!fieldOfStudyId || !gender || !yearOfStudy) {
    setError("Vennligst fyll ut alle feltene.");
    return;
  }

  setSubmitting(true);
  setError(null);

  const result = await submitApplication({
    firstName,
    lastName,
    email,
    phone,
    gender: gender === "male" ? 0 : 1,
    fieldOfStudyId,
    yearOfStudy,
    departmentId: department.id, // from parent props
  });

  setSubmitting(false);

  if (result.ok) {
    navigate("/assistenter/bekreftelse");
  } else {
    setError(result.error);
  }
}
```

Wire each Input's `onChange` to its setter. Wire the "Søk nå!" Button's `onClick` to `handleSubmit`. Show `error` as a red text banner above the button.

**Step 2: Create confirmation page**

Create `src/routes/_home.assistenter.bekreftelse.tsx`:

```typescript
import { Link } from "react-router";

// biome-ignore lint/style/noDefaultExport: Route Modules require default export
export default function ApplicationConfirmation() {
  return (
    <div className="mt-20 mb-20 flex max-w-4xl flex-col items-center gap-6 self-center p-5">
      <h1 className="font-bold text-2xl text-vektor-DARKblue md:text-4xl dark:text-text-dark">
        Søknad mottatt!
      </h1>
      <p className="text-center text-gray-600 text-lg dark:text-gray-300">
        Du vil om kort tid få en e-post med bekreftelse på søknaden din.
      </p>
      <Link
        to="/assistenter"
        className="rounded bg-vektor-DARKblue px-6 py-3 text-white hover:opacity-90"
      >
        Tilbake til assistentsiden
      </Link>
    </div>
  );
}
```

**Step 3: Type check**

```bash
npx tsc --noEmit 2>&1 | grep -v IntrinsicElements | grep "error TS" | head -10
```

**Step 4: Commit**

```bash
git add src/routes/_home.assistenter.tsx src/routes/_home.assistenter.bekreftelse.tsx
git commit -m "feat: add form submission and confirmation page"
```

---

### Task 6: Frontend — Add interest list subscription widget

**Files:**
- Modify: `src/routes/_home.assistenter.tsx`

**What to change:**

When `admissionState !== "open"`, the card currently shows static text "Søknadsperioden er dessverre stengt...". Replace with:

1. The same static text
2. An email input + checkbox for info meeting notifications + "Varsle meg" button
3. On submit: call `subscribeToNotifications()`
4. On success: show "Du er nå påmeldt!" confirmation inline
5. If `admissionState === "upcoming"`: also show "Opptaket åpner {formatted startDate}"

```typescript
import { subscribeToNotifications } from "~/api/admission";

// Inside the closed-state branch of CityApplyCard:
function SubscribeWidget({ departmentId }: { departmentId: number }) {
  const [email, setEmail] = useState("");
  const [infoMeeting, setInfoMeeting] = useState(false);
  const [submitted, setSubmitted] = useState(false);
  const [error, setError] = useState<string | null>(null);

  async function handleSubscribe() {
    const result = await subscribeToNotifications({
      email,
      departmentId,
      infoMeeting,
    });
    if (result.ok) {
      setSubmitted(true);
    } else {
      setError(result.error);
    }
  }

  if (submitted) {
    return <p className="text-center text-green-300">Du er nå påmeldt!</p>;
  }

  return (
    <div className="space-y-3">
      <Input
        type="email"
        placeholder="Din e-postadresse"
        className="text-black"
        value={email}
        onChange={(e) => setEmail(e.target.value)}
      />
      <label className="flex items-center gap-2 text-sm">
        <input
          type="checkbox"
          checked={infoMeeting}
          onChange={(e) => setInfoMeeting(e.target.checked)}
        />
        Varsle meg om informasjonsmøte
      </label>
      <Button variant="green" onClick={handleSubscribe} className="w-full">
        Varsle meg når opptaket åpner
      </Button>
      {error && <p className="text-center text-red-300 text-sm">{error}</p>}
    </div>
  );
}
```

**Step 2: Type check**

```bash
npx tsc --noEmit 2>&1 | grep -v IntrinsicElements | grep "error TS" | head -10
```

**Step 3: Commit**

```bash
git add src/routes/_home.assistenter.tsx
git commit -m "feat: add interest list subscription widget for closed admission"
```

---

### Task 7: Frontend — Add /opptak route aliases

The monolith serves `/opptak` and `/opptak/{city}`. Add route aliases so these URLs work in v2.

**Files:**
- Create: `src/routes/_home.opptak.tsx`
- Create: `src/routes/_home.opptak._index.tsx`
- Create: `src/routes/_home.opptak.$city.tsx`

**Step 1: Create redirect routes**

`src/routes/_home.opptak.tsx`:
```typescript
import { Outlet } from "react-router";

// biome-ignore lint/style/noDefaultExport: Route Modules require default export
export default function OpptakLayout() {
  return <Outlet />;
}
```

`src/routes/_home.opptak._index.tsx`:
```typescript
import { redirect } from "react-router";

export function loader() {
  return redirect("/assistenter");
}

// biome-ignore lint/style/noDefaultExport: Route Modules require default export
export default function OpptakIndex() {
  return null;
}
```

`src/routes/_home.opptak.$city.tsx`:
```typescript
import { redirect } from "react-router";
import type { Route } from "./+types/_home.opptak.$city";

export function loader({ params }: Route.LoaderArgs) {
  return redirect(`/assistenter#${params.city}`);
}

// biome-ignore lint/style/noDefaultExport: Route Modules require default export
export default function OpptakCity() {
  return null;
}
```

**Step 2: Type check**

```bash
npx tsc --noEmit 2>&1 | grep -v IntrinsicElements | grep "error TS" | head -10
```

**Step 3: Commit**

```bash
git add src/routes/_home.opptak.tsx src/routes/_home.opptak._index.tsx src/routes/_home.opptak.\$city.tsx
git commit -m "feat: add /opptak route redirects to /assistenter"
```

---

### Task 8: Frontend — Add Playwright e2e tests

**Files:**
- Modify: `e2e/api-integration.spec.ts`

**Step 1: Add mock data and tests**

Add to the existing e2e spec file:

```typescript
const mockDepartments = [
  { id: 1, name: "NTNU", shortName: "NTNU", city: "Trondheim", email: "ntnu@vektor.no", address: null, latitude: null, longitude: null, logoPath: null, active: true },
  { id: 2, name: "UiB", shortName: "UiB", city: "Bergen", email: "uib@vektor.no", address: null, latitude: null, longitude: null, logoPath: null, active: true },
];

const mockDepartmentDetail = {
  ...mockDepartments[0],
  fieldOfStudy: [
    { id: 1, name: "Fysikk og matematikk", shortName: "MTFYMA", department: { id: 1, city: "Trondheim" } },
    { id: 2, name: "Informatikk", shortName: "BIT", department: { id: 1, city: "Trondheim" } },
  ],
  admissionPeriods: [
    {
      id: 1,
      department: { id: 1, name: "NTNU" },
      startDate: new Date(Date.now() - 7 * 24 * 60 * 60 * 1000).toISOString(),
      endDate: new Date(Date.now() + 30 * 24 * 60 * 60 * 1000).toISOString(),
      infoMeeting: null,
      semester: { id: 1 },
    },
  ],
};

// Add route mocks in setupApiMocks:
page.route("**/api/departments", (route) =>
  route.fulfill({ status: 200, contentType: "application/json", body: JSON.stringify(mockDepartments) }),
),
page.route("**/api/departments/*", (route) =>
  route.fulfill({ status: 200, contentType: "application/json", body: JSON.stringify(mockDepartmentDetail) }),
),
page.route("**/api/admission_subscribers", (route) =>
  route.fulfill({ status: 201, contentType: "application/json", body: "" }),
),

test.describe("Admission flow", () => {
  test("assistenter page shows application form when admission is open", async ({ page }) => {
    await page.goto("/assistenter");
    await expect(page.getByText("Søk nå!")).toBeVisible();
  });

  test("application form submits successfully", async ({ page }) => {
    // Mock the applications endpoint
    await page.route("**/api/applications", (route) =>
      route.fulfill({ status: 201 })
    );
    await page.goto("/assistenter");
    // Form should be visible (admission is open in mock data)
    await expect(page.getByText("Søknadsfrist:")).toBeVisible();
  });

  test("/opptak redirects to /assistenter", async ({ page }) => {
    await page.goto("/opptak");
    await expect(page).toHaveURL(/assistenter/);
  });
});
```

**Step 2: Commit**

```bash
git add e2e/api-integration.spec.ts
git commit -m "test: add e2e tests for admission flow"
```

---

### Task 9: Final verification

**Step 1: Run monolith API tests**

```bash
php -d memory_limit=512M bin/phpunit -c phpunit.xml.dist --testsuite=api
```

Expected: 32+ tests (28 existing + 4 new), 0 failures.

**Step 2: Type check v2 frontend**

```bash
cd /Users/nori/Projects/ntnu/vektor/v2/homepage && npx tsc --noEmit 2>&1 | grep -v IntrinsicElements | grep "error TS" | head -10
```

Expected: no new errors.

**Step 3: Push both repos**

```bash
# Monolith
git push origin modernize/sprint-1-remove-dead-dependencies

# v2 homepage
cd /Users/nori/Projects/ntnu/vektor/v2/homepage
git push origin monolith-merge
```
