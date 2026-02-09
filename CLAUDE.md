# Vektorprogrammet Monolith

Norwegian tutoring program management platform. Symfony 6.4, PHP 8. Upgrading from 3.4 → 6.4.

## Status
- Sprints 1-7b: COMPLETE (Sf6 upgrade + test regression fixes)
- Sprint 8-9: NEXT (DI migration, attributes, frontend)
- Details: `.planning/STATE.md`

## Docs
Each file covers one topic. Load only what you need to keep context small.

| File | Topic |
|------|-------|
| `docs/overview.md` | Quick reference: commands, structure, CI, test users |
| `docs/testing.md` | Test commands, workflow, environment |
| `docs/testing-details.md` | File-to-test map, timing, DB internals |
| `docs/architecture.md` | Controllers, roles, services, mailer |
| `docs/console-commands.md` | Useful Symfony console commands |
| `.planning/test-baseline.md` | Test counts baseline for regression tracking |
| `.github/workflows/ci.yml` | CI config (lint + analyse + test) |

**`docs/`** = for both humans and AI agents. **`.planning/`** = for AI agents only (migration state, task plans).

## Critical Gotchas
- Tests MUST run with `dangerouslyDisableSandbox: true` (SQLite + vendor reads)
- All 496 tests pass (0 known failures)
- HEREDOC in git commit fails in sandbox — use plain quoted strings

## Testing
- Always run tests after code changes. Verify test count matches baseline before committing.
- Track baseline in `.planning/test-baseline.md` — if failure count grows, flag immediately.
- After large refactors, run the FULL suite and compare to baseline (not just filtered tests).
- Never commit if new failures appear that weren't in the baseline.

## Symfony/PHP Conventions
- Preserve bundle shorthand references (e.g. `AppBundle:Entity:User`) — do NOT convert to FQCN unless explicitly asked.
- Never use broad `replace_all` without verifying scope — check for variable names, method names, and unrelated matches first.
- No broad autodiscovery in service config without verifying autowiring compatibility.
- After editing YAML config, validate no duplicate top-level keys were introduced.

## Skill Routing

```
User says something
  ├── quick/obvious fix → just do it
  ├── concrete task (2-5 files) → /rapid-task
  ├── multiple independent tasks → /batch-exec
  ├── vague/big/multi-phase → /fresh-context-planner
  ├── unknown codebase → /codebase-mapper first
  ├── "where was I" / session start → /sprint-continue
  ├── "ready for PR" / "ship" → /pr-prep
  └── "verify" / after changes → /verification-runner
```

## Skill Composition

```
Skills
├── rapid-task (single concrete task, 2-5 files)
│   └── verification-runner
├── batch-exec (N independent tasks in parallel)
│   └── rapid-task[] → verification-runner[]
├── fresh-context-planner (big/vague → atomic plan)
│   └── plan-executor → verification-runner
├── codebase-mapper (understand before changing)
├── sprint-continue (resume from STATE.md + test baseline)
├── pr-prep (last mile → merge)
│   └── code-review, commit-commands
└── state-tracker (persist decisions/progress, not user-invocable)
```

## Planning
- For multi-step tasks, create a written plan BEFORE starting implementation.
- Break large changes into phases. Commit after each phase with tests passing.
- When continuing from a previous session, read `.planning/` docs first.

## Context Management
- For large multi-file changes (50+ files), save progress to `.planning/` periodically.
- If approaching context limits, prioritize: commit current work, write status summary, then stop.
- Context quality degrades: 0-30% peak, 50%+ rushing, 70%+ hallucinations.
- Spawn fresh agent when context >50% used.
- Keep agent prompts minimal: only files + task + verification.

## Conventions
- State lives in `.planning/STATE.md`
- Plans use XML task format in `.planning/phases/`
- Verification reports in `.verification/`
- No planning files for quick fixes
- Use TaskCreate/TaskUpdate for multi-step work within a session
- Use subagents (Task tool) for parallel independent work

## Workflow
**Start**: read `.planning/STATE.md`, run tests against baseline, check `git status`/`git log`
**Dev**: small commits per logical change. Run `--filter=RelevantTest` between changes. Full suite before commit.
**End**: update STATE.md, test-baseline.md, and CLAUDE.md if decisions changed. Leave tests passing.
