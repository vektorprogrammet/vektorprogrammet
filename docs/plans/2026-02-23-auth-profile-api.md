# Auth Profile API Implementation Plan

> **For Claude:** REQUIRED SUB-SKILL: Use superpowers:executing-plans to implement this plan task-by-task.

**Goal:** Expose the authenticated user's profile as read/write API endpoints (`GET /api/me`, `PUT /api/me`) using a dedicated DTO pattern.

**Architecture:** ProfileResource DTO with custom provider (GET) and processor (PUT). JWT authentication via existing LexikJWTAuthenticationBundle. First auth-required API Platform endpoint.

**Tech Stack:** Symfony 6.4, API Platform 3.4, LexikJWTAuthenticationBundle, PHPUnit

**Design doc:** `docs/plans/2026-02-23-auth-profile-api-design.md`

---

## Context for the Implementer

### Key files to reference
- Existing DTO pattern: `src/App/ApiResource/ContactMessageInput.php`, `src/App/ApiResource/Statistics.php`
- Existing provider: `src/App/State/StatisticsProvider.php`
- Existing processor: `src/App/State/ContactMessageProcessor.php`
- User entity: `src/App/Entity/User.php` (getters: `getId()`, `getFirstName()`, `getLastName()`, `getUserName()`, `getEmail()`, `getPhone()`, `getGender()`, `getFieldOfStudy()`)
- User setters: `setFirstName()`, `setLastName()`, `setEmail()`, `setPhone()`, `setGender()`
- Security config: `config/security.yml`
- API Platform config: `config/api_platform.yml`
- Services: `config/services.yml` (auto-wires `App\State\` namespace)
- Test pattern: `tests/AppBundle/Api/ContentApiTest.php`
- Test base: `tests/BaseWebTestCase.php`

### Test users (from fixtures)
- `admin` / `1234` (ROLE_ADMIN)
- `teammember` / `1234` (ROLE_TEAM_MEMBER)
- `assistent` / `1234` (ROLE_USER)

### How to run tests
```bash
composer test -- --filter ProfileApiTest
```
Always use `composer test` (not raw `php bin/phpunit`) — it sets the 256M memory limit.

### Important: sandbox
All test commands require `dangerouslyDisableSandbox: true` (SQLite + vendor reads).

---

## Task 1: Create ProfileResource DTO

**Files:**
- Create: `src/App/ApiResource/ProfileResource.php`

**Step 1: Create the DTO class**

```php
<?php

namespace App\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Put;
use App\State\ProfileProcessor;
use App\State\ProfileProvider;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    operations: [
        new Get(
            uriTemplate: '/me',
            provider: ProfileProvider::class,
            security: "is_granted('ROLE_USER')",
        ),
        new Put(
            uriTemplate: '/me',
            provider: ProfileProvider::class,
            processor: ProfileProcessor::class,
            security: "is_granted('ROLE_USER')",
        ),
    ],
)]
class ProfileResource
{
    public ?int $id = null;

    #[Assert\NotBlank]
    public string $firstName = '';

    #[Assert\NotBlank]
    public string $lastName = '';

    public ?string $userName = null;

    #[Assert\NotBlank]
    #[Assert\Email]
    public string $email = '';

    public ?string $phone = null;

    public ?int $gender = null;

    public ?array $fieldOfStudy = null;
}
```

**Step 2: Verify the file is created**

Run: `php -l src/App/ApiResource/ProfileResource.php`
Expected: `No syntax errors detected`

**Step 3: Commit**

```bash
git add src/App/ApiResource/ProfileResource.php
git commit -m "feat: add ProfileResource DTO for /api/me endpoint"
```

---

## Task 2: Create ProfileProvider (GET /api/me)

**Files:**
- Create: `src/App/State/ProfileProvider.php`

**Step 1: Create the provider class**

```php
<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\ApiResource\ProfileResource;
use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;

class ProfileProvider implements ProviderInterface
{
    public function __construct(
        private Security $security,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): ProfileResource
    {
        /** @var User $user */
        $user = $this->security->getUser();

        return self::fromUser($user);
    }

    public static function fromUser(User $user): ProfileResource
    {
        $profile = new ProfileResource();
        $profile->id = $user->getId();
        $profile->firstName = $user->getFirstName();
        $profile->lastName = $user->getLastName();
        $profile->userName = $user->getUserName();
        $profile->email = $user->getEmail();
        $profile->phone = $user->getPhone();
        $profile->gender = $user->getGender();

        $fos = $user->getFieldOfStudy();
        if ($fos) {
            $profile->fieldOfStudy = [
                'id' => $fos->getId(),
                'name' => $fos->getName(),
                'shortName' => $fos->getShortName(),
            ];
        }

        return $profile;
    }
}
```

**Step 2: Verify syntax**

Run: `php -l src/App/State/ProfileProvider.php`
Expected: `No syntax errors detected`

**Step 3: Commit**

```bash
git add src/App/State/ProfileProvider.php
git commit -m "feat: add ProfileProvider for GET /api/me"
```

---

## Task 3: Create ProfileProcessor (PUT /api/me)

**Files:**
- Create: `src/App/State/ProfileProcessor.php`

**Step 1: Create the processor class**

```php
<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\ApiResource\ProfileResource;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

class ProfileProcessor implements ProcessorInterface
{
    public function __construct(
        private Security $security,
        private EntityManagerInterface $em,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): ProfileResource
    {
        assert($data instanceof ProfileResource);

        /** @var User $user */
        $user = $this->security->getUser();

        $user->setFirstName($data->firstName);
        $user->setLastName($data->lastName);
        $user->setEmail($data->email);
        $user->setPhone($data->phone);
        $user->setGender($data->gender);

        $this->em->flush();

        return ProfileProvider::fromUser($user);
    }
}
```

**Step 2: Verify syntax**

Run: `php -l src/App/State/ProfileProcessor.php`
Expected: `No syntax errors detected`

**Step 3: Commit**

```bash
git add src/App/State/ProfileProcessor.php
git commit -m "feat: add ProfileProcessor for PUT /api/me"
```

---

## Task 4: Add security access control for /api/me

The `api` firewall already covers `^/api` with JWT. However, we need an explicit access_control entry for `/api/me` that requires authentication, placed **before** the catch-all `^/api` rule.

**Files:**
- Modify: `config/security.yml`

**Step 1: Add access control entry**

In `config/security.yml`, in the `access_control` section, add this line **before** the existing `- { path: ^/api, roles: IS_AUTHENTICATED_FULLY }` line:

```yaml
        - { path: ^/api/me, roles: IS_AUTHENTICATED_FULLY }
```

This goes right before:
```yaml
        - { path: ^/api, roles: IS_AUTHENTICATED_FULLY }
```

**Note:** The `^/api` catch-all already requires `IS_AUTHENTICATED_FULLY`, so `/api/me` would be covered anyway. But adding an explicit entry is good practice for documentation and in case the catch-all changes later.

**Step 2: Commit**

```bash
git add config/security.yml
git commit -m "feat: add /api/me access control entry"
```

---

## Task 5: Write tests for GET /api/me

**Files:**
- Create: `tests/AppBundle/Api/ProfileApiTest.php`

**Step 1: Write the test class**

```php
<?php

namespace Tests\AppBundle\Api;

use Tests\BaseWebTestCase;

class ProfileApiTest extends BaseWebTestCase
{
    private function getJwtToken(string $username = 'admin', string $password = '1234'): string
    {
        $client = static::createClient();
        $client->request('POST', '/api/login', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'username' => $username,
            'password' => $password,
        ]));

        $this->assertResponseIsSuccessful();
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('token', $data);

        return $data['token'];
    }

    // --- GET /api/me ---

    public function testGetProfileRequiresAuthentication(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/me', [], [], [
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseStatusCodeSame(401);
    }

    public function testGetProfileReturnsAuthenticatedUser(): void
    {
        $token = $this->getJwtToken('admin', '1234');

        $client = static::createClient();
        $client->request('GET', '/api/me', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer ' . $token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseIsSuccessful();
        $profile = json_decode($client->getResponse()->getContent(), true);

        // Verify expected fields are present
        $this->assertArrayHasKey('id', $profile);
        $this->assertArrayHasKey('firstName', $profile);
        $this->assertArrayHasKey('lastName', $profile);
        $this->assertArrayHasKey('userName', $profile);
        $this->assertArrayHasKey('email', $profile);
        $this->assertArrayHasKey('phone', $profile);
        $this->assertArrayHasKey('gender', $profile);

        // Verify identity — admin fixture user
        $this->assertEquals('admin', $profile['userName']);
        $this->assertIsInt($profile['id']);
    }

    public function testGetProfileDoesNotExposeSensitiveFields(): void
    {
        $token = $this->getJwtToken('admin', '1234');

        $client = static::createClient();
        $client->request('GET', '/api/me', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer ' . $token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseIsSuccessful();
        $profile = json_decode($client->getResponse()->getContent(), true);

        // These fields must NEVER appear in the profile response
        $this->assertArrayNotHasKey('password', $profile);
        $this->assertArrayNotHasKey('companyEmail', $profile);
        $this->assertArrayNotHasKey('accountNumber', $profile);
        $this->assertArrayNotHasKey('new_user_code', $profile);
        $this->assertArrayNotHasKey('roles', $profile);
        $this->assertArrayNotHasKey('isActive', $profile);
    }

    public function testGetProfileWithDifferentUser(): void
    {
        $token = $this->getJwtToken('assistent', '1234');

        $client = static::createClient();
        $client->request('GET', '/api/me', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer ' . $token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseIsSuccessful();
        $profile = json_decode($client->getResponse()->getContent(), true);

        $this->assertEquals('assistent', $profile['userName']);
    }
}
```

**Step 2: Run the tests**

Run: `composer test -- --filter ProfileApiTest`
Expected: All 4 tests pass. If GET /api/me returns 404, the DTO routing isn't being picked up — check `config/api_platform.yml` maps `src/App/ApiResource`.

**Step 3: Commit**

```bash
git add tests/AppBundle/Api/ProfileApiTest.php
git commit -m "test: add GET /api/me tests"
```

---

## Task 6: Write tests for PUT /api/me

**Files:**
- Modify: `tests/AppBundle/Api/ProfileApiTest.php`

**Step 1: Add PUT tests to the existing file**

Append these methods to the `ProfileApiTest` class:

```php
    // --- PUT /api/me ---

    public function testUpdateProfileRequiresAuthentication(): void
    {
        $client = static::createClient();
        $client->request('PUT', '/api/me', [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode([
            'firstName' => 'Updated',
            'lastName' => 'Name',
            'email' => 'updated@example.com',
        ]));

        $this->assertResponseStatusCodeSame(401);
    }

    public function testUpdateProfileFields(): void
    {
        $token = $this->getJwtToken('teammember', '1234');

        $client = static::createClient();

        // First get current profile
        $client->request('GET', '/api/me', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer ' . $token,
            'HTTP_ACCEPT' => 'application/json',
        ]);
        $original = json_decode($client->getResponse()->getContent(), true);

        // Update profile
        $client->request('PUT', '/api/me', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer ' . $token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode([
            'firstName' => 'UpdatedFirst',
            'lastName' => 'UpdatedLast',
            'email' => 'updated-tm@example.com',
            'phone' => '99887766',
            'gender' => 1,
        ]));

        $this->assertResponseIsSuccessful();
        $updated = json_decode($client->getResponse()->getContent(), true);

        $this->assertEquals('UpdatedFirst', $updated['firstName']);
        $this->assertEquals('UpdatedLast', $updated['lastName']);
        $this->assertEquals('updated-tm@example.com', $updated['email']);
        $this->assertEquals('99887766', $updated['phone']);

        // id and userName should not change
        $this->assertEquals($original['id'], $updated['id']);
        $this->assertEquals($original['userName'], $updated['userName']);
    }

    public function testUpdateProfileValidatesEmail(): void
    {
        $token = $this->getJwtToken('teammember', '1234');

        $client = static::createClient();
        $client->request('PUT', '/api/me', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer ' . $token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode([
            'firstName' => 'Test',
            'lastName' => 'User',
            'email' => 'not-an-email',
        ]));

        $this->assertResponseStatusCodeSame(422);
    }

    public function testUpdateProfileValidatesRequiredFields(): void
    {
        $token = $this->getJwtToken('teammember', '1234');

        $client = static::createClient();
        $client->request('PUT', '/api/me', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer ' . $token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode([
            'firstName' => '',
            'lastName' => '',
            'email' => '',
        ]));

        $this->assertResponseStatusCodeSame(422);
    }
```

**Step 2: Run all ProfileApiTest tests**

Run: `composer test -- --filter ProfileApiTest`
Expected: All 8 tests pass.

**Step 3: Commit**

```bash
git add tests/AppBundle/Api/ProfileApiTest.php
git commit -m "test: add PUT /api/me tests"
```

---

## Task 7: Run full test suite

**Files:** None (verification only)

**Step 1: Run the complete test suite**

Run: `composer test`
Expected: 519+ tests, 0 failures. The new tests should add 8 tests to the baseline.

**If tests fail:** Check for conflicts with existing tests. The `teammember` fixture user is modified in Task 6's update test — since each test gets a fresh database (SQLite), this should not cause issues. If it does, investigate whether `BaseWebTestCase` shares static clients across tests.

**Step 2: Commit (if any fixes were needed)**

Only commit if fixes were required. Otherwise, no commit needed.

---

## Task 8: Verify API docs and clean up

**Files:** None (verification only)

**Step 1: Check that OpenAPI docs include the new endpoints**

Start the dev server and visit `/api/docs` (or use curl):

```bash
php bin/console debug:router | grep "api/me"
```

Expected: Two routes matching `/api/me` (GET and PUT).

**Step 2: Run full test suite one final time**

Run: `composer test`
Expected: All tests pass, including the 8 new ProfileApiTest tests.

**Step 3: Final commit if anything was adjusted**

If any adjustments were needed during verification, commit them.

---

## Troubleshooting

### "404 Not Found" on GET /api/me
- Check `config/api_platform.yml` includes `'%kernel.project_dir%/src/App/ApiResource'` in `mapping.paths`
- Run `php bin/console debug:router | grep me` to see if the route is registered
- Clear cache: `php bin/console cache:clear --env=test`

### "401 Unauthorized" when token is provided
- Verify JWT keys exist: `ls config/jwt/private.pem config/jwt/public.pem`
- Check the `api` firewall in `config/security.yml` has `jwt: ~`
- Verify token format: `Authorization: Bearer <token>` (note the space after "Bearer")

### "500 Internal Server Error" on PUT
- Check that `ProfileProcessor::process()` returns a `ProfileResource` (not void)
- Verify EntityManager flush doesn't throw constraint violations
- Check Symfony profiler for the actual exception

### Validation errors (422) when they shouldn't occur
- API Platform deserializes JSON → ProfileResource before validation
- Make sure PUT sends all required fields (`firstName`, `lastName`, `email` are `#[Assert\NotBlank]`)

### FOS REST interference
- The `api` firewall at `^/api` with `jwt: ~` should handle `/api/me`
- FOS REST is scoped to `^/api/party` (zone config) — should not interfere
- If you get HTML instead of JSON, check `fos_rest.yml` zone config
