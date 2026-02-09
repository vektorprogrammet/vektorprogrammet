<img src="https://github.com/vektorprogrammet/vektorprogrammet/blob/master/app/Resources/assets/images/vektor_stor.png" alt="alt text" width="400" height="auto">

# Vektorprogrammet

Management platform for Vektorprogrammet, a Norwegian student organization that provides free tutoring in STEM subjects to middle and high school students.

## Tech Stack

- **PHP** >= 8.1
- **Symfony** 6.4 (LTS)
- **Doctrine ORM** 2.x with SQLite (dev/test) or MySQL (prod)
- **Twig** 3.x
- **Node** 14 (frontend build)

## Setup

### Requirements

- PHP 8.1+ with extensions: `pdo_sqlite`, `gd`, `mbstring`, `curl`, `xml`
- [Composer](https://getcomposer.org/)
- [Node.js](https://nodejs.org/) 14+
- [Git](https://git-scm.com/)

### Install

```bash
git clone https://github.com/vektorprogrammet/vektor-backend.git
cd vektor-backend
composer install
npm install
npm run build:dev
```

### Database Setup

```bash
php bin/console doctrine:schema:create --env=dev
php bin/console doctrine:fixtures:load --env=dev -n
```

### Start Server

```bash
php -S localhost:8000 -t public
```

Or with the Symfony CLI: `symfony server:start`

## Users

| Role | Username | Password | Symfony Role |
|------|----------|----------|--------------|
| Assistent | `assistent` | `1234` | ROLE_USER |
| Teammedlem | `teammember` | `1234` | ROLE_TEAM_MEMBER |
| Teamleder | `teamleader` | `1234` | ROLE_TEAM_LEADER |
| Admin | `admin` | `1234` | ROLE_ADMIN |

## Testing

See [`docs/testing.md`](docs/testing.md) for full details on test suites, timing, and workflow.

```bash
# Quick unit tests (<1s)
bin/phpunit --testsuite=unit

# Controller tests (~110s)
bin/phpunit --testsuite=controller

# Availability smoke tests (~67s)
bin/phpunit --testsuite=availability

# Full suite (496 tests, ~3 min)
bin/phpunit
```

## Project Structure

```
src/App/
  Controller/     # ~55 controllers extending BaseController
  Entity/         # Doctrine entities (annotation-mapped)
  Service/        # Business logic services
  Role/           # Role hierarchy
  EventSubscriber/# Kernel event subscribers
  Twig/           # Twig extensions
  Command/        # Console commands
templates/        # Twig templates
config/           # Symfony config (YAML)
tests/AppBundle/  # PHPUnit tests
docs/             # Developer documentation
.planning/        # Migration planning state
```

## Architecture

- `BaseController` extends `AbstractController` with bridge methods for `getDoctrine()` and `get()` (Sf6 compatibility, pending migration to constructor DI)
- Role hierarchy: `ROLE_USER` < `ROLE_TEAM_MEMBER` < `ROLE_TEAM_LEADER` < `ROLE_ADMIN`
- `AccessControlService` manages route-level access rules with lazy-loaded cache
- URLs are in Norwegian (e.g., `/kontrollpanel/utlegg`, `/opptak`)

## Code Style

```bash
./bin/php-cs-fixer fix src/ --dry-run --diff -vv  # check
./bin/php-cs-fixer fix src/ -vv                    # fix
```

## Legacy npm Scripts

Some npm scripts in `package.json` still work as shortcuts:

```bash
npm run test          # runs PHPUnit
npm run build:dev     # builds frontend assets via gulp
npm run db:reload     # reloads dev database with fixtures
```
