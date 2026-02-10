# Vektorprogrammet Monolith

Symfony 6.4 / PHP 8 platform (upgraded from 3.4). Norwegian tutoring program management.

## Status
- Sprints 1-8: COMPLETE (Sf6 upgrade, constructor DI, PHP 8 attributes)
- Sprint 9: IN PROGRESS (routes + deprecation fixes done, cleanup remaining)
- Details: `.planning/STATE.md`

## Docs
| File | Topic |
|------|-------|
| `docs/overview.md` | Commands, structure, CI, test users |
| `docs/conventions.md` | Code conventions and patterns |
| `docs/troubleshooting.md` | Error → fix lookup |
| `docs/testing.md` | Test commands, workflow, environment |
| `docs/testing-details.md` | File-to-test map, timing, DB internals |
| `docs/architecture.md` | Controllers, roles, services, mailer |
| `docs/knowledge-workflow.md` | How insights flow from staging → docs |
| `.planning/test-baseline.md` | Test baseline for regression tracking |
| `.planning/BOARD.md` | Work board (backlog/todo/in-progress/review/done) |

`docs/` = humans + AI. `.planning/` = AI only.

## Agent Gotchas
- Tests: `dangerouslyDisableSandbox: true` always (SQLite + vendor reads)
- HEREDOC in git commit fails in sandbox — use plain quoted strings
- 496 tests pass, 0 failures. Never commit if new failures appear.
- `composer test` not raw `php bin/phpunit` (sets 256M memory)

## Skills & Agents

### Skills (all from nori-workflow plugin — available in all projects)
```
quick/obvious fix           -> just do it
session start / "continue"  -> /orchestrate
vague/big/multi-phase       -> /plan
"test this" / "run tests"   -> /test
"log decisions" / insights  -> /capture
"sync knowledge" / promote  -> /knowledge-sync (promote only)
"review my code"            -> /review
"ready for PR" / "ship"     -> /pr-prep
agent design questions      -> /agent-expert
research agent patterns     -> /agent-researcher
kanban board                -> /board
```

### Agents (delegated by orchestrator)
```
clear 2-5 file task         -> coding agent
execute PLAN.md task        -> execute-plan agent
run tests/lint/baseline     -> verify agent
update state + knowledge    -> state-sync agent
explore codebase            -> Explore (built-in)
```

## Workflow
- **Start**: `/orchestrate` — reads STATE.md, checks git/tests, presents status, waits for confirmation
- **Dev**: orchestrator sequences coding agent -> verify agent -> state-sync agent per task
- **End** (Stop hook auto-reminds if staging has entries):
  1. Commit work, spawn state-sync agent. Leave tests passing.
  2. Run `/capture` to log session insights, then `/knowledge-sync` to promote staged entries.
  3. Update CLAUDE.md only if agent workflow changed.
- Multi-step: `/plan` first, then `/orchestrate` to execute. Commit per task.
- Context >50%: spawn fresh agent. Commit work + write status before stopping.
- Never broad `replace_all` without verifying scope. After editing YAML, check for duplicate keys.
