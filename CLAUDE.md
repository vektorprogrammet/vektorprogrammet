# Retained Symfony Server

Symfony 6.4 / PHP 8.5 application. It is the current production backend and the
legacy behavior source for the native migration. Do not add a second migration
architecture inside it.

## Commands

| Command | Purpose |
|---|---|
| `composer test` | Run the PHPUnit suite |
| `composer test:parallel` | Run ParaTest with four workers |
| `composer test:unit` | Run unit tests |
| `composer analyse` | Run PHPStan |
| `composer deptrac` | Check context boundaries |
| `composer syntax` | Parse PHP files |
| `composer lint` | Check PHP-CS-Fixer rules |
| `composer fix` | Apply PHP-CS-Fixer rules |

Clear the cache after namespace or configuration changes:

```bash
php bin/console cache:clear
php -d memory_limit=512M bin/phpunit
```

## Structure

Code is grouped by business context:

- `Admission`: applications and admission.
- `Interview`: scheduling, assessment, and recommendation.
- `Organization`: departments, teams, boards, and positions.
- `Survey`: questionnaires and responses.
- `Identity`: users, authentication, and access.
- `Scheduling`: assistant placement.
- `Operations`: receipts, certificates, and work history.
- `Content`: public content and feedback.
- `Shared`: semester and shared interfaces.
- `Support`: mail, SMS, provider adapters, and utilities.

Within a context:

- `Domain` contains pure rules, values, events, and repository interfaces.
- `Infrastructure` contains Doctrine entities and adapters.
- `Api` contains API Platform resources, providers, and processors.
- `Controller` and `Form` are retained Twig paths.

Domain code must not import Symfony or Doctrine. Infrastructure may cross
contexts where Doctrine requires it. New native behavior belongs in the
top-level `packages` and `apps/backend`, not in this application.

Production data cleanup, schema changes, credentials, and deployment require
explicit operator authority.

After a database constraint or validation change, verify fixtures:

```bash
APP_ENV=test php bin/console doctrine:fixtures:load --no-interaction
```
