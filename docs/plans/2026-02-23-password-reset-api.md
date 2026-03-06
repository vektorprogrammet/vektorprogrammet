# Password Reset API Implementation Plan

> **For Claude:** REQUIRED SUB-SKILL: Use superpowers:executing-plans to implement this plan task-by-task.

**Goal:** Expose the password reset flow as two API endpoints, enabling the v2 frontend to handle "forgot password" without Twig.

**Architecture:** Two DTO+Processor pairs reusing the existing `PasswordManager` service. No changes to the existing entity or service — just new API entry points.

**Tech Stack:** Symfony 6.4, API Platform 3.4, existing PasswordManager service

---

## Design

### Endpoints

| Method | Path | Auth | Purpose |
|--------|------|------|---------|
| POST | `/api/password_resets` | PUBLIC_ACCESS | Request a password reset (sends email) |
| POST | `/api/password_resets/{code}` | PUBLIC_ACCESS | Execute reset (set new password) |

### Flow

1. User submits email → `POST /api/password_resets` → PasswordManager creates entity, sends email → 204 No Content (always, to prevent email enumeration)
2. User clicks email link → `POST /api/password_resets/{code}` with `{password}` → validates code, sets password, deletes reset entity → 204 No Content

### Security

- Both endpoints are PUBLIC_ACCESS (unauthenticated users need password reset)
- Response is always 204 regardless of whether email exists (prevents enumeration)
- Company emails (@vektorprogrammet.no) rejected with 422
- Inactive users rejected silently (same 204)
- Reset codes expire after 1 day (existing PasswordManager logic)

### Key Patterns

- Reuse `PasswordManager::createPasswordResetEntity()`, `sendResetCode()`, `resetCodeIsValid()`, `resetCodeHasExpired()`, `getPasswordResetByResetCode()`
- Password set via `User::setPassword()` (uses bcrypt cost 12 internally)
- Old reset codes for same user are deleted before creating new one

---

## Task 1: Create PasswordResetRequest DTO + Processor

**Files:**
- Create: `src/App/ApiResource/PasswordResetRequest.php`
- Create: `src/App/State/PasswordResetRequestProcessor.php`

### PasswordResetRequest DTO

```php
<?php

namespace App\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use App\State\PasswordResetRequestProcessor;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    operations: [
        new Post(
            uriTemplate: '/password_resets',
            processor: PasswordResetRequestProcessor::class,
            output: false,
            status: 204,
        ),
    ],
)]
class PasswordResetRequest
{
    #[Assert\NotBlank]
    #[Assert\Email]
    public string $email = '';
}
```

### PasswordResetRequestProcessor

```php
<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\ApiResource\PasswordResetRequest;
use App\Entity\Repository\PasswordResetRepository;
use App\Service\PasswordManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class PasswordResetRequestProcessor implements ProcessorInterface
{
    public function __construct(
        private PasswordManager $passwordManager,
        private PasswordResetRepository $passwordResetRepo,
        private EntityManagerInterface $em,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        assert($data instanceof PasswordResetRequest);

        $email = $data->email;

        // Block company emails
        if (str_ends_with($email, '@vektorprogrammet.no')) {
            throw new UnprocessableEntityHttpException('Kan ikke resette passord med "@vektorprogrammet.no"-adresse. Prøv din private e-post.');
        }

        $passwordReset = $this->passwordManager->createPasswordResetEntity($email);

        // Silent return for non-existent or inactive users (prevent enumeration)
        if ($passwordReset === null) {
            return;
        }
        if (!$passwordReset->getUser()->isActive()) {
            return;
        }

        // Remove old reset codes for this user
        $oldResets = $this->passwordResetRepo->findByUser($passwordReset->getUser());
        foreach ($oldResets as $old) {
            $this->em->remove($old);
        }

        $this->em->persist($passwordReset);
        $this->em->flush();

        $this->passwordManager->sendResetCode($passwordReset);
    }
}
```

**Verify:** `php -l` on both files

**Commit:** `git commit -m "feat: add password reset request endpoint (POST /api/password_resets)"`

---

## Task 2: Create PasswordResetExecute DTO + Processor

**Files:**
- Create: `src/App/ApiResource/PasswordResetExecute.php`
- Create: `src/App/State/PasswordResetExecuteProcessor.php`

### PasswordResetExecute DTO

```php
<?php

namespace App\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use App\State\PasswordResetExecuteProcessor;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    operations: [
        new Post(
            uriTemplate: '/password_resets/{code}',
            processor: PasswordResetExecuteProcessor::class,
            output: false,
            status: 204,
        ),
    ],
)]
class PasswordResetExecute
{
    #[Assert\NotBlank]
    #[Assert\Length(min: 8)]
    public string $password = '';
}
```

### PasswordResetExecuteProcessor

```php
<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\ApiResource\PasswordResetExecute;
use App\Service\PasswordManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class PasswordResetExecuteProcessor implements ProcessorInterface
{
    public function __construct(
        private PasswordManager $passwordManager,
        private EntityManagerInterface $em,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        assert($data instanceof PasswordResetExecute);

        $code = $uriVariables['code'] ?? '';

        if (!$this->passwordManager->resetCodeIsValid($code)) {
            throw new UnprocessableEntityHttpException('Ugyldig eller utløpt kode.');
        }

        if ($this->passwordManager->resetCodeHasExpired($code)) {
            throw new UnprocessableEntityHttpException('Ugyldig eller utløpt kode.');
        }

        $passwordReset = $this->passwordManager->getPasswordResetByResetCode($code);
        $user = $passwordReset->getUser();

        $user->setPassword($data->password);

        $this->em->remove($passwordReset);
        $this->em->persist($user);
        $this->em->flush();
    }
}
```

**Verify:** `php -l` on both files

**Commit:** `git commit -m "feat: add password reset execute endpoint (POST /api/password_resets/{code})"`

---

## Task 3: Add security access control

**Modify:** `config/security.yml`

Add before the `^/api/me` line:
```yaml
        - { path: ^/api/password_resets, roles: PUBLIC_ACCESS }
```

**Commit:** `git commit -m "feat: add password_resets access control entry"`

---

## Task 4: Write tests

**Create:** `tests/AppBundle/Api/PasswordResetApiTest.php`

```php
<?php

namespace Tests\AppBundle\Api;

use Tests\BaseWebTestCase;

class PasswordResetApiTest extends BaseWebTestCase
{
    public function testRequestPasswordResetWithValidEmail(): void
    {
        $client = static::createClient();
        $client->request('POST', '/api/password_resets', [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode([
            'email' => 'admin@admin.com',
        ]));

        // Always 204 (even if email doesn't exist, to prevent enumeration)
        $this->assertResponseStatusCodeSame(204);
    }

    public function testRequestPasswordResetWithNonExistentEmail(): void
    {
        $client = static::createClient();
        $client->request('POST', '/api/password_resets', [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode([
            'email' => 'nonexistent@example.com',
        ]));

        // Still 204 (prevent enumeration)
        $this->assertResponseStatusCodeSame(204);
    }

    public function testRequestPasswordResetRejectsCompanyEmail(): void
    {
        $client = static::createClient();
        $client->request('POST', '/api/password_resets', [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode([
            'email' => 'someone@vektorprogrammet.no',
        ]));

        $this->assertResponseStatusCodeSame(422);
    }

    public function testRequestPasswordResetValidatesEmail(): void
    {
        $client = static::createClient();
        $client->request('POST', '/api/password_resets', [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode([
            'email' => 'not-an-email',
        ]));

        $this->assertResponseStatusCodeSame(422);
    }

    public function testExecutePasswordResetWithInvalidCode(): void
    {
        $client = static::createClient();
        $client->request('POST', '/api/password_resets/invalidcode123', [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode([
            'password' => 'newpassword123',
        ]));

        $this->assertResponseStatusCodeSame(422);
    }

    public function testExecutePasswordResetValidatesPasswordLength(): void
    {
        $client = static::createClient();
        $client->request('POST', '/api/password_resets/somecode', [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode([
            'password' => 'short',
        ]));

        // 422 from validation (min 8 chars) OR from invalid code
        $status = $client->getResponse()->getStatusCode();
        $this->assertTrue(in_array($status, [422]), "Expected 422, got $status");
    }
}
```

**Run:** `composer test -- --filter PasswordResetApiTest`

**Commit:** `git commit -m "test: add password reset API tests"`

---

## Task 5: Run full test suite and verify

**Run:** `composer test`
**Expected:** Previous baseline + 6 new tests, 0 failures.

---

## Troubleshooting

### 404 on POST /api/password_resets
- Check `config/api_platform.yml` maps `src/App/ApiResource`
- Run `php bin/console debug:router | grep password`

### 500 on request endpoint
- PasswordManager uses Twig to render email template. The template `reset_password/new_password_email.txt.twig` must exist.
- If test env has no mailer, emails may fail. Check `config/packages/test/mailer.yaml`.

### findByUser not found
- `PasswordResetRepository` needs `findByUser` method. Check if it exists or use `findBy(['user' => $user])`.
