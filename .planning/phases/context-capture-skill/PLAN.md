<plan phase="context-capture-skill" created="2026-02-10">

# /context-capture Skill

## Problem

At session end (or on demand), the user asks to "check context for useful insights" or "log decisions." This is manual and easy to forget. Currently handled ad-hoc — sometimes via /knowledge-sync, sometimes by directly editing MEMORY.md/STATE.md.

## Solution

A `/capture` skill that scans conversation context and produces structured updates to the right files. Lightweight — no agents needed, runs in main context since it needs to read the conversation.

## Triggers

- "check context", "capture insights", "log decisions"
- "useful insights", "what did we learn"
- "document decisions", "session summary"
- End-of-session (could replace or complement the Stop hook reminder)

## Workflow

1. **Scan context** for:
   - Architecture/design decisions (with rationale)
   - Gotchas discovered (error → fix patterns)
   - Conventions established or confirmed
   - Tool/workflow insights (what worked, what didn't)
   - State changes (completed tasks, new blockers)

2. **Categorize** each finding:
   | Category | Destination |
   |----------|-------------|
   | Architecture decision | `.planning/STATE.md` decisions section |
   | Error → fix pattern | `docs/troubleshooting.md` |
   | Code convention | `docs/conventions.md` |
   | Workflow/tool insight | MEMORY.md staging section |
   | Task completion | `.planning/STATE.md` progress section |

3. **Present** findings to user as a checklist:
   - Each item with proposed destination and draft text
   - User confirms, edits, or skips each
   - Only write after confirmation

4. **Apply** confirmed items to their destination files

## Relationship to Existing Skills

- **knowledge-sync**: focuses on MEMORY.md staging → docs/ promotion. /capture is broader — it also handles STATE.md and discovers new insights from context.
- **state-sync agent**: updates STATE.md mechanically after tasks. /capture is interactive and covers qualitative insights.
- Could eventually subsume knowledge-sync's "scan" mode.

## Implementation

  <wave id="1" mode="sequential">
    <task id="1">
      <description>Create /capture skill definition</description>
      <agent>coding</agent>
      <context>
        <read>.claude/skills/knowledge-sync/SKILL.md</read>
        <read>.claude/skills/plan/SKILL.md</read>
        <info>Existing skills for reference on format and frontmatter conventions</info>
      </context>
      <action>
        - Create .claude/skills/capture/SKILL.md with frontmatter
        - Include scan categories, routing table, presentation format
        - allowed-tools: Read, Edit, Write, Glob, Grep (no agents needed)
        - Trigger keywords in description
      </action>
      <verification>
        - File exists with valid frontmatter
        - Scan categories cover: decisions, gotchas, conventions, workflow, state
        - Routing table maps each category to a destination file
      </verification>
    </task>
  </wave>

  <wave id="2" mode="sequential">
    <task id="2">
      <description>Update CLAUDE.md routing table and settings</description>
      <agent>coding</agent>
      <context>
        <read>CLAUDE.md</read>
        <read>.claude/settings.local.json</read>
      </context>
      <action>
        - Add /capture to Skills section in CLAUDE.md
        - Add to workflow End section: "Run /capture to log insights"
        - Add Skill(capture) to settings.local.json allow list
      </action>
      <verification>
        - /capture appears in CLAUDE.md Skills routing
        - Skill(capture) in settings.local.json permissions
      </verification>
    </task>
  </wave>

  <wave id="3" mode="sequential">
    <task id="3">
      <description>Evaluate whether /capture replaces knowledge-sync scan mode</description>
      <agent>coding</agent>
      <context>
        <read>.claude/skills/knowledge-sync/SKILL.md</read>
        <read>.claude/skills/capture/SKILL.md</read>
      </context>
      <action>
        - Compare scope of both skills
        - If /capture fully covers knowledge-sync scan: deprecate scan mode, keep promote mode
        - Update Stop hook if needed
      </action>
      <verification>
        - No overlapping trigger keywords between active skills
        - Stop hook references the right skill
      </verification>
    </task>
  </wave>

</plan>

<execution-strategy>
  Wave 1: Create the skill (standalone, no dependencies)
  Wave 2: Wire it into the system (depends on wave 1)
  Wave 3: Evaluate overlap with knowledge-sync (depends on wave 1)
</execution-strategy>
