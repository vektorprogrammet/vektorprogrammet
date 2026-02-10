<plan id="agent-tooling-01">

  <task id="1">
    <description>Add composer scripts for test, lint, fix commands</description>
    <files>composer.json</files>
    <action>
      - Add `scripts` section to composer.json with:
        - `test`: full suite — `/usr/local/opt/php@8.4/bin/php -d memory_limit=512M bin/phpunit -c phpunit.xml.dist`
        - `test:unit`: `@php bin/phpunit --testsuite=unit`
        - `test:controller`: `@php bin/phpunit --testsuite=controller`
        - `test:availability`: `@php bin/phpunit --testsuite=availability`
        - `lint`: `php-cs-fixer fix --dry-run --diff`
        - `fix`: `php-cs-fixer fix`
      - Also add `config.platform.php` if not set, so scripts use the right PHP
      - Verify: `composer run test:unit` runs and passes
    </action>
    <verification>
      - `composer run test:unit` executes correctly
      - `composer run lint` runs php-cs-fixer in dry-run mode
      - `composer.json` is valid JSON
    </verification>
  </task>

  <task id="2">
    <description>Create .php-cs-fixer.dist.php config matching existing code style</description>
    <files>.php-cs-fixer.dist.php</files>
    <action>
      - Create `.php-cs-fixer.dist.php` in project root
      - Target dirs: `src/`, `tests/`
      - Use Symfony ruleset as base (matches existing style)
      - Key rules to match current codebase conventions:
        - `@Symfony` ruleset
        - `array_syntax` => `short`
        - `ordered_imports` => `true`
        - `no_unused_imports` => `true`
        - `single_line_throw` => `false` (legacy code has multiline throws)
        - `yoda_style` => `false` (codebase uses `$var === value` not `value === $var`)
      - Exclude `vendor/`, `var/`, `node_modules/`
      - Add `.php-cs-fixer.cache` to .gitignore
      - Verify: `vendor/bin/php-cs-fixer fix --dry-run` runs without errors
    </action>
    <verification>
      - `.php-cs-fixer.dist.php` exists and is valid PHP
      - `vendor/bin/php-cs-fixer fix --dry-run` completes (may show fixable issues — that's ok)
      - `.php-cs-fixer.cache` is in .gitignore
    </verification>
  </task>

  <task id="3">
    <description>Install and configure PHPStan at level 1</description>
    <files>composer.json, phpstan.neon</files>
    <action>
      - `composer require --dev phpstan/phpstan phpstan/phpstan-symfony phpstan/phpstan-doctrine`
      - Create `phpstan.neon` with:
        - level: 1 (conservative start — catches undefined methods, basic type errors)
        - paths: [src/]
        - symfony container XML path for autowiring resolution
        - excludePaths: generated/cache files
        - includes: phpstan-symfony.neon, phpstan-doctrine.neon extensions
      - Run `vendor/bin/phpstan analyse` and capture baseline if there are existing errors:
        - `vendor/bin/phpstan analyse --generate-baseline`
      - Add `analyse` composer script: `vendor/bin/phpstan analyse`
      - Verify: `composer run analyse` completes with 0 errors (using baseline)
    </action>
    <verification>
      - `vendor/bin/phpstan analyse` exits 0 (baseline may absorb existing errors)
      - phpstan.neon exists and is valid
      - phpstan-baseline.neon exists if needed
      - All 496 tests still pass (phpstan is dev-only, shouldn't affect runtime)
    </verification>
  </task>

  <task id="4">
    <description>Add PostToolUse hook to auto-format PHP files after edits</description>
    <files>~/.claude/settings.json, ~/.claude/hooks/php-cs-fixer-post-edit.sh</files>
    <action>
      - Create `~/.claude/hooks/php-cs-fixer-post-edit.sh`:
        - Triggers on Edit and Write tool results
        - Extracts file_path from $CLAUDE_HOOK_EVENT_DATA
        - Only runs if file ends in `.php`
        - Only runs if `.php-cs-fixer.dist.php` exists in cwd (project has cs-fixer)
        - Runs `vendor/bin/php-cs-fixer fix --quiet $FILE_PATH`
        - Exits 0 always (non-blocking — formatting is best-effort)
      - Add PostToolUse hook entries in ~/.claude/settings.json:
        - matcher: "Edit" → runs php-cs-fixer-post-edit.sh
        - matcher: "Write" → runs php-cs-fixer-post-edit.sh
        - Both async: true (don't block the agent)
      - Verify: edit a PHP file, confirm cs-fixer runs in background
    </action>
    <verification>
      - Hook script exists and is executable
      - settings.json is valid JSON
      - Editing a PHP file triggers the hook (check with a test edit)
    </verification>
  </task>

  <task id="5">
    <description>Clean up settings.local.json permission allow-list</description>
    <files>.claude/settings.local.json</files>
    <action>
      - Remove one-off debug entries:
        - `Bash(/tmp/claude/debug_profile.php:*)`
        - `Bash(/tmp/claude/debug_interview.php:*)`
        - `Bash(tee:*)`
        - `Bash(python3:*)` (not used in PHP project)
      - Remove obsolete PHP 7.4 entry:
        - `Bash(/usr/local/opt/php@7.4/bin/php:*)`
      - Keep all legitimate entries (git, php, composer, gh, etc.)
      - Add `Bash(vendor/bin/php-cs-fixer:*)` for the new linting tools
      - Add `Bash(vendor/bin/phpstan:*)` for static analysis
      - Add `Bash(composer:*)` as a broader pattern (covers `composer run test:unit` etc.)
      - Verify: JSON is valid
    </action>
    <verification>
      - settings.local.json is valid JSON
      - No debug/obsolete entries remain
      - phpstan, php-cs-fixer, composer permissions present
    </verification>
  </task>

  <task id="6">
    <description>Create docs/console-commands.md quick reference</description>
    <files>docs/console-commands.md, CLAUDE.md</files>
    <action>
      - Run `bin/console list` to discover available commands
      - Create `docs/console-commands.md` with the most useful commands for agents:
        - Routing: `debug:router`, `debug:router --show-controllers`
        - DI: `debug:container`, `debug:autowiring`
        - Doctrine: `doctrine:schema:validate`, `doctrine:mapping:info`
        - Cache: `cache:clear`
        - Config: `debug:config`
        - Security: `debug:firewall`
      - Group by use case (understand app, debug issues, DI migration)
      - Keep it short — just command + one-line description + when to use
      - Add pointer in CLAUDE.md docs section
      - Verify: all listed commands actually work
    </action>
    <verification>
      - Each listed command runs without error
      - CLAUDE.md references the new file
      - File is under 40 lines
    </verification>
  </task>

</plan>

<execution-strategy>
  <wave id="1" mode="parallel" note="Independent setup tasks">
    <tasks>1, 2, 5, 6</tasks>
    <notes>
      Composer scripts, cs-fixer config, settings cleanup, and console docs are all independent.
      Task 2 (cs-fixer config) and task 1 (composer scripts) don't depend on each other —
      cs-fixer is already installed, just needs a config file.
    </notes>
  </wave>
  <wave id="2" mode="sequential" note="PHPStan requires composer install">
    <tasks>3</tasks>
    <notes>
      PHPStan install modifies composer.json/lock. Run alone to avoid conflicts
      with wave 1's composer.json edits. Needs the composer scripts from task 1
      to already be in place so we can add the `analyse` script.
    </notes>
  </wave>
  <wave id="3" mode="sequential" note="Hook depends on cs-fixer being configured">
    <tasks>4</tasks>
    <notes>
      The auto-format hook only makes sense after cs-fixer config exists (task 2).
      Also validates the full pipeline works end-to-end.
    </notes>
  </wave>
</execution-strategy>
