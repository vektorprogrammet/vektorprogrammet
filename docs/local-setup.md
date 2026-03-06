# Local Development Setup

Complete guide to setting up Vektorprogrammet locally for development.

## Prerequisites

Before you begin, ensure you have the following installed:

### Required Software

| Software | Version | Check Command |
|----------|---------|---------------|
| **PHP** | 8.1 or higher | `php -v` |
| **Composer** | Latest | `composer --version` |
| **Node.js** | 14 or higher | `node -v` |
| **npm** | Latest | `npm -v` |
| **Git** | Any recent version | `git --version` |

### Required PHP Extensions

Run `php -m` to check installed extensions. You need:

- `pdo_sqlite` (for local development database)
- `pdo_mysql` (optional, for MySQL database)
- `gd` (image processing)
- `mbstring` (multibyte string support)
- `curl` (HTTP requests)
- `xml` (XML parsing)
- `intl` (internationalization)
- `zip` (ZIP archive handling)

**Install missing extensions:**

```bash
# macOS (Homebrew)
brew install php@8.3
brew install php-gd php-mbstring php-curl php-xml php-intl php-zip

# Ubuntu/Debian
sudo apt-get install php8.3 php8.3-sqlite3 php8.3-mysql php8.3-gd php8.3-mbstring php8.3-curl php8.3-xml php8.3-intl php8.3-zip

# Windows
# Enable extensions in php.ini by uncommenting (remove semicolon):
# extension=pdo_sqlite
# extension=gd
# extension=mbstring
# etc.
```

### Optional: Symfony CLI

The Symfony CLI provides a better development server experience with TLS support and automatic PHP version detection.

```bash
# macOS/Linux
curl -sS https://get.symfony.com/cli/installer | bash

# Windows
scoop install symfony-cli
```

## Installation

### 1. Clone Repository

```bash
git clone https://github.com/vektorprogrammet/vektorprogrammet.git
cd vektorprogrammet
```

Or if you have a fork:

```bash
git clone https://github.com/YOUR_USERNAME/vektorprogrammet.git
cd vektorprogrammet
```

### 2. Install PHP Dependencies

```bash
composer install
```

This will:
- Install all Symfony and third-party PHP packages
- Prompt you to configure `parameters.yml` (see Configuration section below)
- Set up autoloading

### 3. Install Node Dependencies

```bash
npm install
```

This installs frontend build tools (Vite) and JavaScript dependencies.

### 4. Configure Application

Copy the parameter template and edit it:

```bash
cp config/parameters.yml.dist config/parameters.yml
```

Edit `config/parameters.yml` with your local settings:

```yaml
parameters:
    # Database (SQLite for local dev - no setup needed!)
    database_driver:   pdo_sqlite
    database_path: "%kernel.project_dir%/var/data/dev.db"

    # For MySQL instead (optional):
    # database_driver:   pdo_mysql
    # database_host:     localhost
    # database_port:     3306
    # database_name:     vektorprogrammet
    # database_user:     root
    # database_password: your_password
    # database_path:     ~ # Set to null for MySQL

    # Email (local dev - emails logged, not sent)
    mailer_transport:  smtp
    mailer_host:       127.0.0.1

    # Disable external services for local dev
    slack_disabled: true
    sms_disable: true

    # Secret key (generate a random string)
    secret: ThisIsASecretChangeMe123456789

    # Email addresses (can use fake emails for dev)
    no_reply_email_user_creation: noreply@localhost
    no_reply_email_contact_form: contact@localhost
    default_from_email: vektorbot@localhost

    # Google API (optional - leave as xxxxx if not using)
    google_api_client_id: 'xxxxx'
    google_api_client_secret: 'xxxxx'
    google_api_refresh_token: 'xxxxx'
```

**Key Configuration Notes:**
- **SQLite** is recommended for local development (zero configuration)
- **MySQL** can be used if you prefer, but requires additional setup
- External services (Slack, SMS) are disabled by default for local dev
- Email is logged to console/files instead of actually being sent

### 5. Set Up Database

#### Option A: SQLite (Recommended for Local Dev)

Create the database schema and load test data:

```bash
php bin/console doctrine:schema:create --env=dev
php bin/console doctrine:fixtures:load --env=dev -n
```

Or use the npm shortcut:

```bash
npm run db:reload
```

#### Option B: MySQL

If using MySQL, first create the database:

```bash
# Log into MySQL
mysql -u root -p

# Create database
CREATE DATABASE vektorprogrammet CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;
```

Then run migrations and load fixtures:

```bash
php bin/console doctrine:migrations:migrate --env=dev -n
php bin/console doctrine:fixtures:load --env=dev -n
```

### 6. Build Frontend Assets

Build JavaScript and CSS assets:

```bash
npm run build:dev
```

This compiles assets from `assets/` to `public/js/` and `public/css/`.

**Build outputs:**
- `public/js/app.js` - Main application JavaScript
- `public/js/controlPanel.js` - Control panel JavaScript
- `public/css/app.css` - Main application styles
- `public/css/control_panel.css` - Control panel styles
- `public/vendor/` - Third-party libraries (CKEditor, Dropzone, etc.)
- `public/images/` - Image assets
- `public/webfonts/` - Font files

### 7. Set Permissions

Ensure the `var/` directory is writable:

```bash
# macOS/Linux
chmod -R 777 var/

# Or more secure:
chmod -R 775 var/
chown -R www-data:www-data var/  # Adjust user/group as needed
```

## Running the Application

### Start the Development Server

#### Option A: Symfony CLI (Recommended)

```bash
symfony server:start
```

The application will be available at `https://127.0.0.1:8000` (with automatic TLS).

#### Option B: PHP Built-in Server

```bash
php -S localhost:8000 -t public
```

The application will be available at `http://localhost:8000`.

### Frontend Development (Watch Mode)

For automatic rebuilding of assets during development:

```bash
npm run watch
```

This watches for changes in `assets/` and rebuilds automatically. Leave it running in a separate terminal.

### Access the Application

Open your browser and navigate to:
- Main site: `http://localhost:8000`
- Login: `http://localhost:8000/login`
- Admin panel: `http://localhost:8000/kontrollpanel`

### Test Users

The fixtures load several test users with different permission levels:

| Username | Password | Role | Description |
|----------|----------|------|-------------|
| `admin` | `1234` | ROLE_ADMIN | Full admin access |
| `teamleader` | `1234` | ROLE_TEAM_LEADER | Team leader access |
| `teammember` | `1234` | ROLE_TEAM_MEMBER | Team member access |
| `assistent` | `1234` | ROLE_USER | Basic assistant access |

## Development Workflow

### Running Tests

```bash
# Run full test suite (496 tests, ~193s sequential)
composer test

# Run tests in parallel (faster, ~103s)
composer test:parallel

# Run only unit tests (<1s)
composer test:unit

# Run only controller tests (~110s)
composer test:controller

# Run only availability tests (~67s)
composer test:availability

# Run specific test class
bin/phpunit --filter=HomeControllerTest

# Run with coverage (WARNING: Full suite coverage deferred (OOM >1GB). See testing.md for unit-only coverage (9%).)
composer test:coverage
```

**Important:** Tests use SQLite and require `dangerouslyDisableSandbox: true` when run via Claude Code agents.

See [docs/testing.md](testing.md) for detailed testing documentation.

### Code Quality

```bash
# Check code style (PHP-CS-Fixer)
composer lint

# Auto-fix code style issues
composer fix

# Run static analysis (PHPStan)
composer analyse
```

### Frontend Development

```bash
# Development build (unminified, with sourcemaps)
npm run build:dev

# Production build (minified, optimized)
npm run build:prod

# Watch mode (auto-rebuild on changes)
npm run watch
```

### Database Management

```bash
# Reload database with fresh fixtures
npm run db:reload

# Manually update schema (SQLite)
php bin/console doctrine:schema:update --force --env=dev

# Load fixtures only
php bin/console doctrine:fixtures:load -n --env=dev

# For MySQL: run migrations instead
php bin/console doctrine:migrations:migrate --env=dev
```

### Useful Symfony Commands

```bash
# See all routes
php bin/console debug:router

# Clear cache
php bin/console cache:clear --env=dev

# See all services
php bin/console debug:container

# Validate database schema
php bin/console doctrine:schema:validate
```

See [docs/console-commands.md](console-commands.md) for more commands.

## Common Issues & Troubleshooting

### Port Already in Use

**Error:** `Address already in use`

**Solution:** Another process is using port 8000.

```bash
# Find process using port 8000
lsof -i :8000

# Kill the process
kill -9 <PID>

# Or use a different port
php -S localhost:8001 -t public
symfony server:start --port=8001
```

### Database Connection Errors

**Error:** `An exception occurred in driver: SQLSTATE[HY000] [2002] Connection refused`

**Solution for SQLite:**
- Verify `database_path` in `config/parameters.yml`
- Ensure `var/data/` directory exists and is writable
- Delete and recreate database: `rm var/data/dev.db && npm run db:reload`

**Solution for MySQL:**
- Verify MySQL is running: `sudo service mysql status`
- Check credentials in `config/parameters.yml`
- Verify database exists: `mysql -u root -p -e "SHOW DATABASES;"`

### Permission Errors (var/cache, var/log)

**Error:** `Failed to create directory "/path/to/var/cache"`

**Solution:**

```bash
# macOS/Linux
rm -rf var/cache/* var/log/*
chmod -R 777 var/

# If you get "disk I/O error" with SQLite
rm -f var/data/test.db var/data/test.db.bk
rm -rf var/cache/test/
```

### Missing PHP Extensions

**Error:** `extension pdo_sqlite is not loaded`

**Solution:**

```bash
# Check installed extensions
php -m

# Install missing extension (Ubuntu/Debian)
sudo apt-get install php8.3-sqlite3

# Enable extension in php.ini (find location with: php --ini)
# Uncomment line: extension=pdo_sqlite
```

### Node/npm Version Mismatch

**Error:** `npm ERR! engine Unsupported engine`

**Solution:**

```bash
# Check Node version (requires 14+)
node -v

# Update Node (using nvm)
nvm install 18
nvm use 18

# Or using Homebrew
brew upgrade node
```

### Vite Build Errors

**Error:** `Could not resolve entry module`

**Solution:**

```bash
# Reinstall node modules
rm -rf node_modules package-lock.json
npm install

# Rebuild
npm run build:dev
```

### Composer Install Fails

**Error:** `Your requirements could not be resolved`

**Solution:**

```bash
# Clear Composer cache
composer clear-cache

# Update Composer itself
composer self-update

# Install with more verbose output
composer install -vvv
```

### Twig Cache Issues (Mass Test Failures)

**Error:** Tests failing after entity or config changes

**Solution:**

```bash
# Clear test cache
rm -rf var/cache/test/

# First test run will recompile ~200 templates (slower)
composer test
```

### Stale Database Schema

**Error:** `no such table: some_table`

**Solution:**

```bash
# For SQLite
php bin/console doctrine:schema:drop --force --env=dev
php bin/console doctrine:schema:create --env=dev
php bin/console doctrine:fixtures:load -n --env=dev

# Shortcut
npm run db:reload

# For MySQL
php bin/console doctrine:migrations:migrate --env=dev
```

## Environment-Specific Configuration

### Development (dev)

- Uses SQLite by default
- Debug mode enabled
- Emails logged to console
- External services (Slack, SMS) disabled
- Detailed error pages

### Testing (test)

- Automatically configured via `config/config_test.yml`
- Uses SQLite (`var/data/test.db`)
- Database reset automatically between tests
- No external service calls

### Production (prod)

- Uses MySQL
- Debug mode disabled
- Full error logging
- External services enabled
- Optimized cache and performance

**Note:** This guide focuses on local development. Production deployment is configured differently.

## Additional Resources

- [docs/overview.md](overview.md) - Project structure, architecture, commands
- [docs/conventions.md](conventions.md) - Coding standards and patterns
- [docs/testing.md](testing.md) - Testing workflow and commands
- [docs/troubleshooting.md](troubleshooting.md) - Extended troubleshooting guide
- [docs/architecture.md](architecture.md) - Application architecture details
- [Symfony Documentation](https://symfony.com/doc/6.4/index.html) - Symfony 6.4 docs

## Getting Help

1. Check [docs/troubleshooting.md](troubleshooting.md) for common errors
2. Search existing GitHub issues
3. Ask in the team Slack/Discord channel
4. Open a new GitHub issue with details

## Quick Start (TL;DR)

For experienced developers who just want to get running:

```bash
# Clone and install
git clone https://github.com/vektorprogrammet/vektorprogrammet.git
cd vektorprogrammet
composer install
npm install

# Configure (edit as needed)
cp config/parameters.yml.dist config/parameters.yml

# Set up database and build assets
npm run db:reload
npm run build:dev

# Start server
symfony server:start
# Or: php -S localhost:8000 -t public

# Run tests
composer test

# Access at http://localhost:8000
# Login with: admin / 1234
```
