<plan phase="plugin-fixes" created="2026-02-10">

  <wave id="1" mode="parallel">
    <task id="1">
      <description>Fix plan skill templates.md path reference</description>
      <agent>coding</agent>
      <context>
        <read>~/.claude/workflow/skills/plan/SKILL.md</read>
        <info>Line 39 says "using the template from `.claude/skills/plan/templates.md`". Now that the skill lives in the plugin at ~/.claude/workflow/skills/plan/, this path is wrong. The templates.md file is in the same directory as SKILL.md, so reference it as just `templates.md` (relative to skill dir).</info>
      </context>
      <action>
        - Edit ~/.claude/workflow/skills/plan/SKILL.md line 39: change `.claude/skills/plan/templates.md` to `templates.md` (same directory as this skill)
      </action>
      <verification>
        - No references to `.claude/skills/plan/` remain in the SKILL.md
        - templates.md reference is relative (just `templates.md`)
      </verification>
    </task>

    <task id="2">
      <description>Fix orchestrator agent spawn paths for plugin context</description>
      <agent>coding</agent>
      <context>
        <read>~/.claude/workflow/skills/orchestrate/SKILL.md</read>
        <info>
          Agent spawn templates reference `.claude/agents/coding.md`, `.claude/agents/verify.md`, etc. These are project-local paths. Since the plugin now ships default agents (task 4), update spawn templates to reference agents by name only — Claude Code resolves agent names across scopes (project > user > plugin).

          Also line 127 references `~/.claude/agents/web.md` which has moved to the plugin.

          Change all spawn templates from "Read .claude/agents/X.md for your role definition" to "You are the X agent" and let Claude Code's agent resolution handle loading the right definition.

          Actually simpler: just use Task tool with subagent_type set to the agent name. Claude Code resolves custom agents by name. Change from subagent_type: "general-purpose" + "Read .claude/agents/X.md" to subagent_type: "coding" (etc). This is the correct Claude Code pattern.
        </info>
      </context>
      <action>
        - Update all Agent Spawn Templates to use named subagent_type instead of "general-purpose" + manual agent file reads:
          - Coding Agent: subagent_type: "coding"
          - Verify Agent: subagent_type: "verify"
          - State-Sync Agent: subagent_type: "state-sync"
          - Execute-Plan Agent: subagent_type: "execute-plan"
          - Web Agent: subagent_type: "web"
        - Remove "Read .claude/agents/X.md for your role definition" from all prompts
        - Keep the task-specific prompt content (e.g., "Then: <task description>")
      </action>
      <verification>
        - No hardcoded .claude/agents/ paths in spawn templates
        - No ~/.claude/agents/ paths in spawn templates
        - All 6 spawn templates use named subagent_type
      </verification>
    </task>

    <task id="3">
      <description>Generalize verify agent for cross-project portability</description>
      <agent>coding</agent>
      <context>
        <read>.claude/agents/verify.md</read>
        <read>.claude/agents/coding.md</read>
        <info>verify.md lines 17-20 hardcode `composer test`, `php-cs-fixer`, `phpstan`. The coding and execute-plan agents were already generalized to discover commands from docs/overview.md. Apply the same pattern to verify.</info>
      </context>
      <action>
        - Replace hardcoded "Checks to Run" section with discovery: "Read docs/overview.md or project CLAUDE.md for test/lint/analysis commands"
        - Keep the baseline comparison step (it's already generic — reads .planning/test-baseline.md)
        - Keep the constraint about dangerouslyDisableSandbox but make it conditional: "Check project CLAUDE.md for test constraints"
        - Remove hardcoded `composer test`, `php-cs-fixer`, `phpstan` references
      </action>
      <verification>
        - No composer/phpunit/php-cs-fixer/phpstan references in verify.md
        - References docs/overview.md for command discovery
        - Return format unchanged
      </verification>
    </task>
  </wave>

  <wave id="2" mode="sequential">
    <task id="4">
      <description>Move generalized agents to plugin as defaults (option c)</description>
      <agent>coding</agent>
      <context>
        <read>.claude/agents/coding.md</read>
        <read>.claude/agents/verify.md</read>
        <read>.claude/agents/execute-plan.md</read>
        <read>.claude/agents/state-sync.md</read>
        <info>
          Option (c): ship template agents in plugin/agents/ that projects can override locally.
          Claude Code scope priority: CLI > project > user > plugin.
          So project .claude/agents/ overrides plugin agents/ automatically.

          Steps:
          1. Generalize all 4 agents — remove remaining project-specific constraints (dangerouslyDisableSandbox, HEREDOC sandbox, specific file paths like docs/conventions.md, docs/troubleshooting.md)
          2. Copy generalized versions to ~/.claude/workflow/agents/
          3. Keep project-local copies with project-specific overrides (or remove them and rely on plugin defaults + CLAUDE.md for project constraints)

          For this project: the project-specific constraints are in CLAUDE.md "Agent Gotchas" section. Agents already reference "check project CLAUDE.md". So we can remove the local copies and let the plugin defaults work — project CLAUDE.md provides the project-specific context.
        </info>
      </context>
      <action>
        - Create generalized versions of all 4 agents:
          - coding.md: Remove dangerouslyDisableSandbox constraint, make Mandatory Reading discover via CLAUDE.md docs table
          - verify.md: Already generalized in task 3. Remove dangerouslyDisableSandbox, generalize file paths
          - execute-plan.md: Remove dangerouslyDisableSandbox and HEREDOC constraints, make Mandatory Reading discoverable
          - state-sync.md: Already mostly generic. Remove specific file path references (.planning/STATE.md hardcoded heading)
        - Copy all 4 to ~/.claude/workflow/agents/
        - Remove project-local .claude/agents/ copies (project CLAUDE.md Agent Gotchas covers the overrides)
        - Commit to plugin repo
      </action>
      <verification>
        - 4 agent .md files exist in ~/.claude/workflow/agents/
        - No dangerouslyDisableSandbox in plugin agent files
        - No composer/phpunit/php-cs-fixer references in plugin agent files
        - Project CLAUDE.md still has Agent Gotchas section for project-specific constraints
      </verification>
    </task>
  </wave>

  <wave id="3" mode="parallel">
    <task id="5">
      <description>Clean up settings.json.template portability</description>
      <agent>coding</agent>
      <context>
        <read>~/.claude/workflow/setup/settings.json.template</read>
        <info>Line 3 hardcodes "/Users/nori/.claude/statusline-command.sh". Replace with a comment placeholder or use a relative path. Also review the ntfy URL — it's personal but that's expected for a personal workflow repo.</info>
      </context>
      <action>
        - Replace hardcoded statusline path with "TODO: set your statusline command path" comment or use $HOME-based path
        - Add a comment in install.sh noting that ntfy URLs and statusline paths should be customized
      </action>
      <verification>
        - No /Users/nori hardcoded paths in settings.json.template (except ntfy URL which is intentionally personal)
        - install.sh mentions customization needed
      </verification>
    </task>

    <task id="6">
      <description>Update CLAUDE.md and MEMORY.md after agent migration</description>
      <agent>coding</agent>
      <context>
        <read>CLAUDE.md</read>
        <info>
          After agents move to plugin:
          - CLAUDE.md Agents section should note they come from plugin with project override capability
          - MEMORY.md pointers should reflect agents are in plugin
          - Add a note in CLAUDE.md that project-specific agent constraints live in Agent Gotchas section
        </info>
      </context>
      <action>
        - Update CLAUDE.md "Agents (delegated by orchestrator)" section to note they're from nori-workflow plugin, overridable locally
        - Update MEMORY.md Pointers to include agents in plugin list
      </action>
      <verification>
        - CLAUDE.md reflects agents are from plugin
        - MEMORY.md pointers accurate
      </verification>
    </task>
  </wave>

</plan>

<execution-strategy>
  Wave 1: parallel — 3 independent fixes (plan path, orchestrator paths, verify generalize)
  Wave 2: sequential — agent migration depends on verify being generalized (task 3)
  Wave 3: parallel — 2 independent cleanups after migration
</execution-strategy>
