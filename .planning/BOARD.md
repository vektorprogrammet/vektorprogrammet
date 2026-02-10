# Work Board

**Updated**: 2026-02-10

## Backlog
_Ideas and future work — no plan written yet._

- PHPStan level increase (beyond current baseline)
- Frontend modernization (asset pipeline, Webpack Encore?)
- PHPUnit 10 upgrade
- SwiftMailer → Symfony Mailer migration
- symfony/flex adoption

## Todo
_Planned and ready to pick up._

- **[S9-1]** Fix `strlen(null)` in `AssetExtension.php:49`
- **[S9-2]** Fix `${var}` interpolation in `AssistantControllerTest.php:63-69`
- **[S9-3]** Fix optional param before required in `SponsorsController::sponsorEditAction()`
- **[S9-4]** Upgrade knp-paginator + sentry-symfony for PHP 8.4

## In Progress
_Actively being worked on._


## To Review
_Done but needs verification or user sign-off._

- **[WF-8]** Pre-commit + test-failure hooks — already implemented in `~/.claude/settings.json` (PreToolUse git commit reminder + PostToolUse phpunit ntfy). Verify if hookify rules overlap.


## Completed
_Recently finished. Archive to STATE.md periodically._

- **[WF-1]** Archive completed phase plans to `.planning/archive/` — sprint-8, agent-tooling moved
- **[WF-2]** Update ROADMAP.md — Milestone 3 IN PROGRESS, added Milestone 4 backlog
- **[WF-3]** Clean STATE.md — already clean, no changes needed
- **[WF-4]** File→test mapping — already in `docs/testing-details.md`
- **[WF-5]** Architecture doc — already at `docs/architecture.md`
- **[WF-6]** Slim CLAUDE.md — already lean (62 lines), removals done in prior sessions
- **[WF-7]** Restructure MEMORY.md — already concise with pointers, error patterns in docs/troubleshooting.md
