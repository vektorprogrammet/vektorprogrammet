---
name: agent-researcher
description: Research AI agent patterns from Anthropic docs and the field. Fetches latest information and updates local references. Use when "research agents", "update agent docs", "latest agent patterns", or need current SDK/framework info.
allowed-tools: Read, Edit, Write, WebSearch, WebFetch, Task
argument-hint: <topic>
---

# Agent Researcher

Fetches current agent design knowledge from the web and maintains the local knowledge base at `.claude/skills/agent-expert/references.md`.

Domain-general. Portable across projects.

## Scope

This skill **researches and updates references**. It does NOT:
- Review or score agent designs (use `/agent-expert` for that)
- Write production agent code
- Make architectural decisions without user confirmation

## Workflow

### 1. Research first (fresh, unbiased)

Spawn a `general-purpose` subagent via Task tool. Do NOT read references.md before this step — the subagent should discover information independently to avoid confirming stale knowledge.

- **Subagent type**: `general-purpose`
- **Max turns**: 15
- **Model**: sonnet
- **Prompt must include**:
  - Role: AI agent architecture researcher
  - Tools: WebSearch, WebFetch
  - Search strategy: 3-5 searches combining topic with:
    - `anthropic <topic> 2025 2026`
    - `claude agent <topic>`
    - `ai agent <topic> best practices`
    - `<topic> agentic architecture patterns`
  - Priority sources: docs.anthropic.com, platform.claude.com, code.claude.com, anthropic.com/engineering, claude.com/blog
  - Output format: Current best practice, Key patterns, Tradeoffs, Sources (actual URLs only)
  - Do NOT pass existing references.md content to the subagent

### 2. Validate sources

After subagent returns, **spot-check 1-2 URLs** with WebFetch. Flag any that 404 or don't match claimed content.

### 3. Compare with existing knowledge

NOW read `.claude/skills/agent-expert/references.md` — specifically the Sources Index and the section relevant to the topic. Compare research findings against what's already documented:
- What's genuinely new?
- What contradicts existing claims?
- What confirms existing knowledge (skip these — no need to duplicate)?

### 4. Update references

Edit `.claude/skills/agent-expert/references.md`:

- **New source**: Add to Sources Index table, create new numbered section with the source's key concepts, recommendations, and quotes.
- **Updated info on existing source**: Edit the relevant section in place. Note the update date.
- **New pattern or principle**: Add to the most relevant existing section, or to Cross-Cutting Principles if it spans sources.
- **Contradiction with existing content**: Replace the old claim, add a note about what changed.

Preserve the file's existing structure: Sources Index → numbered sections by source → Common Failure Modes → Cross-Cutting Principles.

### 5. Report

Tell the user:
- What was found
- What was added/updated in references.md
- Key sources with URLs
- Anything that contradicted prior knowledge

## Research Triggers

When invoked by other skills or the user, use these rules:

**Always research:**
- Specific SDK/framework name (Claude Agent SDK, LangGraph, CrewAI, AutoGen)
- Version number ("SDK v0.5", "Claude 3.5")
- "Latest", "current", "new" keywords
- Specific API endpoint or config field

**Never research (redirect to `/agent-expert`):**
- Pure architecture patterns already in references.md
- Code review requests
- Established concepts (RAG, tool use, prompt chaining)

**Ask user:**
- Everything else

## No topic specified?

Ask the user what they want to research. Suggest browsing the Sources Index in references.md to identify gaps.
