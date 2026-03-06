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

> **New to the project?** See [`docs/local-setup.md`](docs/local-setup.md) for a complete step-by-step guide with troubleshooting.

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

## Development

```bash
composer test      # Run full test suite
composer lint      # Check code style
composer fix       # Auto-fix code style
composer analyse   # Static analysis
```

See [`docs/overview.md`](docs/overview.md) for all commands, project structure, architecture, CI, and test users.

## Documentation

Documentation is split into modular single-topic files to stay focused and reduce noise.

| File | Audience | Topic |
|------|----------|-------|
| `README.md` | Everyone | Setup and entry point |
| [`docs/local-setup.md`](docs/local-setup.md) | New developers | Complete local setup guide with troubleshooting |
| [`docs/overview.md`](docs/overview.md) | Developers | Quick reference: commands, structure, CI, test users |
| [`docs/conventions.md`](docs/conventions.md) | Developers | Code conventions and patterns |
| [`docs/troubleshooting.md`](docs/troubleshooting.md) | Developers | Error → fix lookup |
| [`docs/knowledge-workflow.md`](docs/knowledge-workflow.md) | Everyone | How project knowledge is captured |
| [`docs/testing.md`](docs/testing.md) | Developers | Test commands, workflow, environment |
| [`docs/testing-details.md`](docs/testing-details.md) | Developers | File-to-test map, timing, DB internals |
| [`docs/architecture.md`](docs/architecture.md) | Developers | Controllers, roles, services, mailer |
| [`docs/console-commands.md`](docs/console-commands.md) | Developers | Useful Symfony console commands |
| `CLAUDE.md` | AI agents | Agent workflow and skills |

**Principles**: each doc covers one topic. `README.md` is the entry point; `docs/` has everything else.

## npm Scripts

Some npm scripts in `package.json` work as shortcuts:

```bash
npm run test          # runs PHPUnit
npm run build:dev     # builds frontend assets (development mode)
npm run build:prod    # builds frontend assets (production mode, minified)
npm run watch         # watch mode for frontend development
npm run db:reload     # reloads dev database with fixtures
```
