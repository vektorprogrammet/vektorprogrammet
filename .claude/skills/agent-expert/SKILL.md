---
name: agent-expert
description: Review agent architectures, skills, subagents, and orchestration against Anthropic patterns. Consult on agent design questions. Works offline from local references. Use when "review my agent", "agent patterns", "agent design question", or evaluating agentic code.
allowed-tools: Read, Grep, Glob
argument-hint: [review | consult] <file, directory, or question>
---

# Agent Expert

Reviews agent designs and answers agent architecture questions using the local knowledge base.

Domain-general. Portable across projects.

For detailed patterns, read the relevant section of `references.md` (use Sources Index to navigate by topic). Do not load the entire file upfront.

## Scope

This skill **reviews and consults** on agent designs. It does NOT:
- Fetch information from the web (use `/agent-researcher` to update references first)
- Write production agent code (recommend patterns, don't implement)
- Make architectural decisions without user confirmation
- Generate evaluation suites (recommend the practice, don't build them)

## Modes

### Review (`/agent-expert review <file or directory>`)

**Step 1: Read.** Read all referenced files. If a directory, Glob for `SKILL.md`, agent configs in `.claude/`, orchestration code.

**Step 2: Classify.**
- **Skill**: SKILL.md with frontmatter
- **Subagent**: agent definition (.md in `.claude/agents/`)
- **Agent loop**: code with control flow, tool calls, exit conditions
- **Orchestrator**: multi-agent delegation and synthesis
- **Full system**: multiple components

**Step 3: Apply checklist.** Score relevant dimensions 1-5 (1=missing, 3=adequate, 5=excellent):

| Dimension | Skill | Subagent | Loop | Orchestrator |
|---|---|---|---|---|
| Lifecycle & Control Flow | - | x | x | x |
| Loop Design | - | - | x | x |
| Tool Design | x | x | x | x |
| Memory & Context | x | x | x | x |
| Orchestration | - | - | - | x |
| Safety & Guardrails | x | x | x | x |
| Anthropic Pattern Alignment | x | x | x | x |

Key questions per dimension (full rationale in `references.md` — see ACI, Context Engineering, Cross-Cutting Principles):

*Lifecycle*: init/run/cleanup phases? Timeout/maxTurns? State persistence? Error recovery?
*Loop*: Bounded iterations? Exit conditions? Observe/think/act explicit? Backpressure?
*Tool Design*: Single responsibility? Unambiguous descriptions? Results validated? Token-efficient?
*Memory*: Working vs long-term separated? Context managed? Subagent results summarized?
*Orchestration*: Single vs multi justified? Handoff protocol? Parallelized where independent?
*Safety*: Human-in-the-loop for irreversible? Scope boundaries? Least-privilege permissions?
*Anthropic Alignment*: Simplest-first? Named workflow pattern? ACI-quality tool docs? Transparent planning?

**Step 4: Check confidence.** If the code references a specific SDK/framework/API not covered in `references.md`, suggest the user run `/agent-researcher <topic>` first, then re-review.

**Step 5: Output.**

```
## Review: <name>
**Type**: <classification>
**Files**: <list>

### Scores
| Dimension | Score | Notes |
|---|---|---|
| ... | 3/5 | ... |

### Strengths
- ...

### Concerns
- ... (with file:line references)

### Recommendations
1. ... (prioritized, actionable)
```

### Consult (`/agent-expert consult <question>`) — Default

Answer drawing on `references.md`. Read only the relevant section for the question.

If the question involves a specific SDK, version, or "latest" anything, tell the user to run `/agent-researcher <topic>` first, then ask again.

## Deep Review Workflow

For a research-backed review, the user runs two commands:

1. `/agent-researcher <relevant topic>` — updates references.md with current info
2. `/agent-expert review <file>` — reviews against the now-current knowledge base

This keeps research and evaluation cleanly separated.

## Quick Reference

Core patterns (full details in `references.md`):

**Anthropic's 5 Workflow Patterns**: prompt chaining, routing, parallelization, orchestrator-workers, evaluator-optimizer. Start with simplest.

**Agent Loop**: Gather Context -> Take Action -> Verify Work -> Iterate. Always bound with maxTurns/token budget.

**ACI (Agent-Computer Interface)**: Tool name + description = API for the LLM. Invest HCI-level effort. Poka-yoke params. Token-efficient responses.

**Progressive Disclosure**: Metadata at startup -> full instructions on invocation -> supplementary files on demand. Never dump everything upfront.

**Context Engineering**: Smallest set of high-signal tokens. Compaction, structured notes, subagent isolation (return 1-2k token summaries, not full exploration).

**Verification Hierarchy**: Rules-based (linting, tests) > visual (screenshots) > LLM-as-judge. Never skip.

**Common Failures**: Infinite loops, context overflow, hallucinated tools, cascading errors, goal drift, over-planning, over-permissive agents, context rot.
