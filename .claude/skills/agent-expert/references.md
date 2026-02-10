# Agent Expert: Reference Material

Quick-reference for agent design patterns, sourced from Anthropic's published engineering guidance.
Knowledge base for `/agent-expert` (reader) and `/agent-researcher` (writer).
Agents: read only the section relevant to your current question. Humans: browse by source or topic.

---

## Sources Index

| # | Title | URL | Key Topic |
|---|-------|-----|-----------|
| 1 | Building Effective Agents | [anthropic.com/research/building-effective-agents](https://www.anthropic.com/research/building-effective-agents) | 5 workflow patterns, agents vs workflows |
| 2 | Writing Tools for AI Agents | [anthropic.com/engineering/writing-tools-for-agents](https://www.anthropic.com/engineering/writing-tools-for-agents) | ACI, 5 tool design principles |
| 3 | Effective Harnesses for Long-Running Agents | [anthropic.com/engineering/effective-harnesses-for-long-running-agents](https://www.anthropic.com/engineering/effective-harnesses-for-long-running-agents) | Session management, progress tracking |
| 4 | Context Engineering for AI Agents | [anthropic.com/engineering/effective-context-engineering-for-ai-agents](https://www.anthropic.com/engineering/effective-context-engineering-for-ai-agents) | Context rot, compaction, progressive disclosure |
| 5 | Equipping Agents with Agent Skills | [anthropic.com/engineering/equipping-agents-for-the-real-world-with-agent-skills](https://www.anthropic.com/engineering/equipping-agents-for-the-real-world-with-agent-skills) | Skill composition, progressive disclosure |
| 6 | Building Agents with Claude Agent SDK | [anthropic.com/engineering/building-agents-with-the-claude-agent-sdk](https://www.anthropic.com/engineering/building-agents-with-the-claude-agent-sdk) | Agent loop, verification, subagent patterns |
| 7 | Claude Code Subagents Documentation | [code.claude.com/docs/en/sub-agents](https://code.claude.com/docs/en/sub-agents) | Subagent configuration, delegation patterns, lifecycle |
| 8 | Claude Agent SDK Subagents API | [platform.claude.com/docs/en/agent-sdk/subagents](https://platform.claude.com/docs/en/agent-sdk/subagents) | Programmatic subagent definition, resumption |

---

## 1. Workflow Patterns (Source 1)

**When to use what:**

| Approach | Best For |
|----------|----------|
| Single LLM call | Most tasks (optimized with retrieval + examples) |
| Workflows | Predictable decomposition, latency-accuracy tradeoffs |
| Agents | Open-ended problems where steps can't be predetermined |

**The 5 workflow patterns:**

1. **Prompt Chaining** — Sequential steps, each LLM call processes previous output. Add programmatic gates between steps. _Use for: fixed, decomposable subtasks._

2. **Routing** — Classify input, direct to specialized handler. _Use for: distinct categories needing separate processing._

3. **Parallelization** — Two mechanisms:
   - *Sectioning*: independent subtasks run simultaneously (e.g., guardrail + content generation)
   - *Voting*: same task multiple times for consensus
   _Use for: speed or confidence._

4. **Orchestrator-Workers** — Central LLM dynamically decomposes, delegates to workers, synthesizes. _Use for: subtasks that can't be predicted in advance._

5. **Evaluator-Optimizer** — One LLM generates, another evaluates in a loop. _Use for: tasks with clear eval criteria where iteration improves output._

**Core principle:** "Start with the simplest solution possible, only increase complexity when needed."

---

## 2. Tool Design as ACI (Sources 2, 6)

**ACI = Agent-Computer Interface.** Invest the same effort in tool design as you would in user interface design.

### 5 Tool Design Principles

1. **Choose tools strategically** — More tools != better. Consolidate multi-step operations into single tools. Use `schedule_event` not separate `list_users` + `list_events` + `create_event`.

2. **Namespace clearly** — Group by service + resource: `asana_projects_search`, `jira_tasks_create`. Clear boundaries reduce confusion.

3. **Return meaningful context** — Semantic names over UUIDs. Exclude low-signal fields. Implement `response_format` parameter (`"concise"` / `"detailed"`).

4. **Optimize token efficiency** — Truncate large results with instructions for refinement. Provide actionable error messages, not stack traces. Default response limit (Claude Code uses 25k tokens).

5. **Engineer descriptions precisely** — Write as you'd brief a new team member. Make implicit context explicit. Clarify specialized query syntax. Use unambiguous parameter names (`user_id` not `user`).

### Additional principles
- Idempotent where possible
- Format close to natural text (avoid line counting, heavy escaping)
- Apply poka-yoke: design params to make mistakes harder (e.g., absolute paths only)
- Fail loudly with actionable errors

**Key insight:** "We spent more time optimizing our tools than the overall prompt." (SWE-bench team)

---

## 3. Context Engineering (Source 4)

**Core problem:** Model accuracy degrades as context length increases. Context is a precious, finite resource.

**Guiding rule:** "Find the smallest possible set of high-signal tokens maximizing desired outcomes."

### Techniques for Long-Horizon Tasks

1. **Compaction** — Summarize conversation history as context limits approach. Preserve architectural decisions and critical details; discard redundant outputs.

2. **Structured note-taking** — Write persistent notes outside the context window, pulled back when needed. Enables multi-hour coherence.

3. **Sub-agent architectures** — Specialized agents handle focused tasks with clean context windows. Each returns condensed summaries (1,000-2,000 tokens) rather than full exploration data.

### System Prompt Design
- Balance specificity and flexibility
- Organize with XML tags or Markdown headers
- Start minimal, add instructions based on failure modes
- Diverse canonical examples > exhaustive edge case lists

### Dynamic Context Retrieval
- **Just-in-time**: maintain lightweight identifiers (file paths, queries), retrieve on demand
- **Hybrid**: some data upfront for speed, autonomous exploration afterward
- Claude Code exemplifies this: CLAUDE.md loaded initially; grep/glob for runtime navigation

---

## 4. Long-Running Agent Harnesses (Source 3)

### Initializer + Coding Agent Pattern

**Initializer** (first session):
- Creates startup scripts, progress documentation, initial commit
- Generates comprehensive feature list with completion criteria

**Coding Agent** (each subsequent session):
1. Read progress files + git history
2. Select single highest-priority incomplete feature
3. Implement with descriptive commits
4. Update progress docs before session end
5. Verify via end-to-end testing

### Session Startup Routine
1. Confirm working directory
2. Read git logs + progress files
3. Select highest-priority incomplete feature
4. Run basic tests before implementing new features

### Key rules
- One feature per session (prevents context exhaustion)
- Never mark done without verification
- Never remove tests to make things pass
- Structured progress files alongside git history

---

## 5. Skill Design: Progressive Disclosure (Source 5)

### Three-Tier Information Loading

1. **Metadata** — Skill name + description loaded at startup into system prompt. Enables efficient routing without consuming full context.
2. **Core instructions** — Full SKILL.md loaded when Claude determines relevance.
3. **Supplementary materials** — Referenced files loaded contextually as needed.

"The amount of context that can be bundled into a skill is effectively unbounded" since agents with filesystem access only read necessary materials.

### Recommendations
- Start with evaluation: identify capability gaps through representative task testing
- Split unwieldy SKILL.md into separate, logically-organized materials
- Monitor actual usage patterns and iterate
- Ask Claude to capture successful workflows into reusable skill components
- Audit skills from untrusted sources (check code deps and bundled resources)
- Use deterministic scripts when a sorting/parsing task doesn't need LLM generation

---

## 6. Agent Loop & Verification (Source 6)

### Four-Stage Agent Loop
1. **Gather context** — Fetch and update information
2. **Take action** — Execute using available tools
3. **Verify work** — Evaluate output quality
4. **Iterate** — Repeat until successful

### Context Gathering Hierarchy (in order of preference)
1. **Agentic search + filesystem** — Treat folder/file structure as context engineering. Start here.
2. **Semantic search** — Faster but less accurate, harder to maintain. Use second.
3. **Subagents** — For parallelization and context isolation.
4. **Context compaction** — Automatic summarization at limits.

### Verification Approaches (ranked by robustness)
1. **Rules-based feedback** — Most robust. Linting, tests, type checking. TypeScript > JavaScript for this reason.
2. **Visual feedback** — Screenshots/renders via Playwright MCP for UI tasks.
3. **LLM-as-judge** — Least robust. "Generally not a very robust method." Use only when marginal gains justify cost.

---

## 9. Claude Code Specifics (Sources 1, 5, 7, 8)

### Skill Frontmatter Fields

| Field | Description | Required |
|-------|-------------|----------|
| `name` | Display name (lowercase, hyphens, max 64 chars). Defaults to directory name. | No |
| `description` | What it does and when to use (max 1024 chars). Defaults to first paragraph. | Recommended |
| `argument-hint` | Autocomplete hint, e.g. `[issue-number]` | No |
| `allowed-tools` | Tools Claude can use without asking when active | No |
| `user-invocable` | `false` hides from `/` menu. Default: `true` | No |
| `disable-model-invocation` | `true` prevents auto-loading. Default: `false` | No |
| `model` | Model override while active | No |
| `context` | Set to `fork` to run in subagent context | No |
| `agent` | Subagent type when `context: fork` (default: `general-purpose`) | No |
| `hooks` | Hooks scoped to this skill's lifecycle | No |

**Not skill fields:** `memory` (subagent-only), `version` (undocumented), `mode` (undocumented).

### Built-in Subagent Types

| Type | Model | Tools | Purpose |
|------|-------|-------|---------|
| Explore | Haiku | Read-only | Codebase search |
| Plan | Inherits | Read-only | Research for planning |
| general-purpose | Inherits | All | Complex multi-step tasks |
| Bash | Inherits | Terminal | Command execution |
| claude-code-guide | Haiku | N/A | Claude Code questions |
| statusline-setup | Sonnet | N/A | Status line config |

**Key constraint:** Subagents cannot spawn other subagents. Chain from main conversation instead.

### Custom Subagents

**Three definition methods:**
1. **File-based**: Markdown with YAML frontmatter in `.claude/agents/` (project) or `~/.claude/agents/` (user)
2. **CLI-based**: JSON via `--agents` flag (session-scoped, not saved to disk)
3. **Programmatic**: SDK's `AgentDefinition` in `agents` parameter (TypeScript/Python)

**Scope priority** (highest to lowest): CLI flag > project > user > plugin

**Frontmatter fields**: `name`, `description`, `tools`, `disallowedTools`, `model`, `permissionMode`, `maxTurns`, `skills`, `mcpServers`, `hooks`, `memory`.

**Memory scopes**: `user` (~/.claude/agent-memory/), `project` (.claude/agent-memory/), `local` (.claude/agent-memory-local/). First 200 lines of MEMORY.md auto-injected into prompt.

**CLI example:**
```bash
claude --agents '{
  "code-reviewer": {
    "description": "Expert code reviewer",
    "prompt": "System prompt here...",
    "tools": ["Read", "Grep", "Glob"],
    "model": "sonnet"
  }
}'
```

**SDK example (TypeScript):**
```typescript
import { query } from '@anthropic-ai/claude-agent-sdk';

for await (const msg of query({
  prompt: "Review this code",
  options: {
    allowedTools: ['Read', 'Grep', 'Task'],
    agents: {
      'code-reviewer': {
        description: 'Expert reviewer',
        prompt: 'System prompt...',
        tools: ['Read', 'Grep'],
        model: 'sonnet'
      }
    }
  }
})) { /* ... */ }
```

**Dynamic agent creation** (factory pattern):
```typescript
function createSecurityAgent(level: 'basic' | 'strict'): AgentDefinition {
  return {
    description: 'Security reviewer',
    prompt: `You are a ${level} security reviewer...`,
    tools: ['Read', 'Grep', 'Glob'],
    model: level === 'strict' ? 'opus' : 'sonnet'
  };
}
```

### Hook Event Types

| Event | Fires When | Can Block? |
|-------|------------|------------|
| SessionStart | Session begins/resumes | No |
| UserPromptSubmit | Before processing user input | Yes |
| PreToolUse | Before tool executes | Yes |
| PermissionRequest | Permission dialog appears | Yes |
| PostToolUse | After tool succeeds | No |
| PostToolUseFailure | After tool fails | No |
| SubagentStart | Subagent spawned | No |
| SubagentStop | Subagent finishes | Yes |
| Stop | Claude finishes responding | Yes |
| PreCompact | Before context compaction | No |
| SessionEnd | Session terminates | No |

Hook handler types: `command` (shell), `prompt` (single-turn LLM), `agent` (multi-turn LLM with tools).

### Subagent Delegation Patterns (Sources 7, 8)

**Invocation methods:**
- **Automatic**: Claude delegates based on task description matching subagent's `description` field
- **Explicit**: Mention subagent by name in prompt ("Use the code-reviewer agent to...")
- **Built-in general-purpose**: Available when `Task` in allowedTools, without defining custom agents

**Critical requirement:** Task tool MUST be in allowedTools for subagent invocation. Subagents are invoked via Task tool internally.

**Restricting spawnable agents:** Use `Task(agent_type)` syntax in tools field to allowlist specific subagents:
```yaml
tools: Task(worker, researcher), Read, Bash  # Only worker and researcher can be spawned
```

**Execution modes:**
- **Foreground (blocking)**: Permission prompts passed through. Clarifying questions (AskUserQuestion) work.
- **Background (concurrent)**: Pre-approve permissions upfront. Auto-denies unapproved tools. No MCP tools. No clarifying questions. Press Ctrl+B to background a running task.

Disable background tasks via `CLAUDE_CODE_DISABLE_BACKGROUND_TASKS=1`.

**Parallel execution:** Multiple subagents can spawn within milliseconds for independent tasks. Each maintains separate context. Example: style-checker, security-scanner, test-coverage running simultaneously.

**Best practice:** Limit to 3-4 subagents maximum. More than that reduces productivity due to delegation overhead.

**Delegation instructions:** Explicit orchestration > cautious auto-delegation. Provide detailed steps including which subagents handle which parts. Like programming with threads, explicit control yields best results.

### Subagent Resumption and Context (Sources 7, 8)

**Resumption:** Each subagent invocation creates fresh context by default. To continue existing work, ask Claude to resume by agent ID.

**Agent ID:** Returned in Task tool result. Also found in `~/.claude/projects/{project}/{sessionId}/subagents/agent-{agentId}.jsonl`.

**Transcript persistence:**
- Stored per subagent, independent of main conversation
- Survive main conversation compaction
- Persist within session (can resume after restart by resuming session)
- Auto-cleanup based on `cleanupPeriodDays` (default: 30 days)

**Auto-compaction:** Triggers at ~95% capacity (override via `CLAUDE_AUTOCOMPACT_PCT_OVERRIDE`). Logged in transcript with `compact_boundary` system event.

**SDK resumption pattern (TypeScript):**
```typescript
let agentId, sessionId;

// First query captures session_id and agentId from results
for await (const msg of query({ prompt: "...", options: {...} })) {
  if ('session_id' in msg) sessionId = msg.session_id;
  // Extract agentId from Task tool result content
}

// Second query resumes same session
for await (const msg of query({
  prompt: `Resume agent ${agentId} and...`,
  options: { resume: sessionId, /* same agent definitions */ }
})) { /* ... */ }
```

**Detecting subagent invocation:** Check for `tool_use` blocks with `name: "Task"`. Messages within subagent context have `parent_tool_use_id` field.

### `/agents` Command Interface (Source 7)

Interactive interface for managing subagents. Run `/agents` to:
- View all available subagents (built-in, user, project, plugin)
- Create new subagents with guided setup or Claude generation
- Edit existing subagent configuration and tool access
- Delete custom subagents
- See which subagents are active when duplicates exist (priority order shown)

**Creation workflow:**
1. Choose scope (user-level or project-level)
2. Generate with Claude or write manually
3. Select tools (can deselect to create read-only agents)
4. Choose model (sonnet, opus, haiku, inherit)
5. Pick color for UI identification
6. Save (no restart needed, immediately available)

**Note:** File-based agents loaded at session start. If creating via file manually (not `/agents`), restart session to load.

### Dynamic Context Injection

`` !`command` `` syntax in SKILL.md runs shell commands during preprocessing. Output replaces the placeholder before content reaches Claude.

---

## Common Failure Modes

| Failure | Cause | Prevention |
|---------|-------|------------|
| Infinite loops | No exit condition | maxTurns, iteration budget |
| Context overflow | Unbounded accumulation | Compaction, subagent summaries |
| Hallucinated tools | Tools that don't exist | Strict tool lists, validation |
| Cascading errors | One failure poisons chain | Isolate with subagents, fail fast |
| Goal drift | Loses original objective | Structured progress files, re-read goals |
| Over-planning | Planning replaces doing | "Start simple" principle |
| Over-permissive agents | bypassPermissions when not needed | Least-privilege tool access |
| Context rot | Quality degrades at length | Fresh subagents per task, compaction |
| Premature completion | Declares done without testing | Mandatory verification step |
| Subagent gatekeeping | Specialized subagent hides context from main | Give main agent context, let it delegate via Task |
| Task not in allowedTools | Subagents can't be invoked | Include Task in allowedTools |
| Too many subagents | Delegation overhead > productivity gain | Limit to 3-4 subagents maximum |
| Background subagent permission failures | Missing pre-approved permissions | Pre-approve upfront or resume in foreground |

---

## Cross-Cutting Principles

These themes appear across all 6 sources:

1. **Simplicity as default** — Start with single LLM call + retrieval. Add workflows. Add agents. Each step only when demonstrated improvement justifies complexity.

2. **ACI deserves HCI-level investment** — Tool descriptions matter more than system prompts. Evaluate, iterate, measure.

3. **Progressive disclosure** — Load minimal metadata first. Let agents pull detail on demand. Never dump everything upfront.

4. **Structured state for multi-session** — Progress files, git history, feature lists. One feature per session.

5. **Verification is non-negotiable** — Rules-based > visual > LLM-as-judge. Never mark done without testing. Never remove tests.

6. **Token efficiency is architecture** — Semantic context over raw dumps. Subagents return summaries. Compact history, preserving decisions.

7. **Evaluation-driven development** — Build eval suites mirroring real workflows. Analyze transcripts for failure patterns. Iterate tools, prompts, skills equally.
