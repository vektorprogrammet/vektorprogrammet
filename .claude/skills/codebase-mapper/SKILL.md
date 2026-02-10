---
name: codebase-mapper
description: Analyze codebase structure, stack, patterns, and concerns. Use when "analyze code", "understand codebase", "what's the stack", or exploring brownfield project.
allowed-tools: Read, Grep, Glob, Bash(*)
argument-hint: [deep]
user-invocable: false
---

# Codebase Mapper

Map BEFORE planning. Understand existing code so planning questions focus on the new feature, not rediscovering what exists.

## Default Mode (Single Pass)

Produce a single `README.md` covering:

1. **Stack**: Languages, frameworks, tools, DB, key dependencies
2. **Architecture**: Layers, patterns, request flow, state management
3. **Structure**: Directory layout, naming conventions, module organization
4. **Testing**: Framework, how to run, coverage level
5. **Concerns**: Tech debt, security considerations, known issues

Investigate by reading:
- Config files (composer.json, package.json, Cargo.toml, etc.)
- Directory structure (top 2 levels)
- Entry points (index, main, kernel, routes)
- Test configuration
- CI/CD config
- Existing documentation

Output: `.planning/codebase/README.md`

## Deep Mode (`/codebase-mapper deep`)

Spawn parallel agents via the Task tool, one per area:

1. **Stack agent** → STACK.md
2. **Architecture agent** → ARCHITECTURE.md
3. **Structure agent** → STRUCTURE.md
4. **Conventions agent** → CONVENTIONS.md
5. **Testing agent** → TESTING.md
6. **Concerns agent** → CONCERNS.md

Each agent reads only the files relevant to its area and produces a focused report.

Output:
```
.planning/codebase/
├── README.md (summary linking to details)
├── STACK.md
├── ARCHITECTURE.md
├── STRUCTURE.md
├── CONVENTIONS.md
├── TESTING.md
└── CONCERNS.md
```
