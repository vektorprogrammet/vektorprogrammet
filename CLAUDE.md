# Vektorprogrammet Monolith

Symfony 6.4 / PHP 8 platform (upgraded from 3.4). Norwegian tutoring program management.

## Status
- Sprints 1-8: COMPLETE (Sf6 upgrade, constructor DI, PHP 8 attributes)
- Sprint 9: IN PROGRESS (routes, deprecations, paratest, package cleanup done)
- Details: `memory/STATE.md` (in project memory directory)

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

`docs/` = humans + AI. AI state lives in project memory directory (not in repo).

## Agent Gotchas
- Tests: `dangerouslyDisableSandbox: true` always (SQLite + vendor reads)
- HEREDOC in git commit fails in sandbox — use plain quoted strings
- 496 tests pass, 0 failures. Never commit if new failures appear.
- `composer test` not raw `php bin/phpunit` (sets 256M memory)

## Skills & Agents

### Skills (all from nori-workflow plugin — available in all projects)
```
quick/obvious fix           -> just do it
delegate task / subagent    -> /delegate
vague/big/multi-phase       -> /plan
"test this" / "run tests"   -> /test
"log decisions" / insights  -> /capture
"review" / "evaluate"       -> /review (code + domain routing)
"ready for PR" / "ship"     -> /pr-prep
"consult" / agent questions -> /consult
research agent patterns     -> /agent-researcher
kanban board                -> /board
```

### Agents (from nori-workflow plugin, overridable locally in `.claude/agents/`)
```
clear 2-5 file task         -> coding agent
run tests/lint/baseline     -> verify agent
explore codebase            -> Explore (built-in)
web research                -> web agent
agent design review/consult -> agent-expert (subagent)
```

## Workflow
- **Start**: Orchestrator reads STATE.md, checks git/tests, presents status, waits for confirmation
- **Dev**: orchestrator sequences coding agent -> verify agent per task
- **End** (Stop hook auto-reminds if staging has entries):
  1. Commit work. Leave tests passing.
  2. Run `/capture` to log session insights.
  3. Update CLAUDE.md only if agent workflow changed.
- Multi-step: `/plan` first, then execute per wave with `/delegate`. Commit per task.
- Context >50%: spawn fresh agent. Commit work + write status before stopping.
- Never broad `replace_all` without verifying scope. After editing YAML, check for duplicate keys.
