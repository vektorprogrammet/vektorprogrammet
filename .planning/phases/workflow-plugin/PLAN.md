<plan phase="workflow-plugin" created="2026-02-10">

  <wave id="1" mode="parallel">
    <task id="1">
      <description>Fix broken format-post-edit hook path in settings.local.json</description>
      <agent>coding</agent>
      <context>
        <read>.claude/settings.local.json</read>
        <info>Lines 70-82: PostToolUse hooks for Edit and Write reference /Users/nori/.claude/hooks/format-post-edit.sh but the file is at .claude/hooks/format-post-edit.sh (local). Change both to: bash "$CLAUDE_PROJECT_DIR"/.claude/hooks/format-post-edit.sh</info>
      </context>
      <action>
        - Edit .claude/settings.local.json lines 70 and 80: replace the absolute global path with "$CLAUDE_PROJECT_DIR"/.claude/hooks/format-post-edit.sh
      </action>
      <verification>
        - The two PostToolUse hook commands in settings.local.json use $CLAUDE_PROJECT_DIR
        - No other references to the old absolute path remain
      </verification>
    </task>

    <task id="2">
      <description>Move pre-commit phpunit hook from global settings to local settings</description>
      <agent>coding</agent>
      <context>
        <read>/Users/nori/.claude/settings.json</read>
        <read>.claude/settings.local.json</read>
        <info>Global settings.json lines 49-59 has a PreToolUse hook that warns about running phpunit before commit. This is PHP-specific and should be in settings.local.json instead. Move it to local, remove from global.</info>
      </context>
      <action>
        - Add the PreToolUse hook block from ~/.claude/settings.json to .claude/settings.local.json under the hooks section
        - Remove the PreToolUse hook block from ~/.claude/settings.json
      </action>
      <verification>
        - settings.local.json has the PreToolUse hook for Bash with the phpunit commit warning
        - ~/.claude/settings.json no longer has the PreToolUse section
        - Both files are valid JSON
      </verification>
    </task>

    <task id="3">
      <description>Remove empty agent memory scaffolds and frontmatter</description>
      <agent>coding</agent>
      <context>
        <read>.claude/agents/coding.md</read>
        <read>.claude/agents/verify.md</read>
        <read>.claude/agent-memory/coding/MEMORY.md</read>
        <read>.claude/agent-memory/verify/MEMORY.md</read>
        <info>Agent memory files are empty scaffolds adding context noise. Remove the memory: project line from coding.md and verify.md frontmatter. Delete the .claude/agent-memory/ directory entirely.</info>
      </context>
      <action>
        - Edit .claude/agents/coding.md: remove the "memory: project" line from frontmatter
        - Edit .claude/agents/verify.md: remove the "memory: project" line from frontmatter
        - Delete .claude/agent-memory/ directory (rm -rf)
      </action>
      <verification>
        - coding.md and verify.md frontmatter has no memory: line
        - .claude/agent-memory/ directory does not exist
      </verification>
    </task>
  </wave>

  <wave id="2" mode="sequential">
    <task id="4">
      <description>Generalize capture skill for cross-project portability</description>
      <agent>coding</agent>
      <context>
        <read>.claude/skills/capture/SKILL.md</read>
        <info>Currently hardcodes docs/troubleshooting.md, docs/conventions.md, docs/architecture.md, .planning/STATE.md. Generalize: skill should discover docs/ directory presence, look for CLAUDE.md doc table to find targets, fall back to docs/ convention. Keep the same scan categories but make destinations discoverable.</info>
      </context>
      <action>
        - Replace hardcoded path references with discovery logic: "Read project CLAUDE.md for docs table. If no table, check for docs/ directory. Use discovered paths as targets."
        - Update the scan categories table to show target pattern rather than exact paths
        - Add a "Discovery" section explaining how it finds target files
      </action>
      <verification>
        - No hardcoded project-specific paths remain in SKILL.md
        - Skill describes how to discover project doc structure
        - Scan categories still cover the same types of insights
      </verification>
    </task>

    <task id="5">
      <description>Generalize knowledge-sync skill for cross-project portability</description>
      <agent>coding</agent>
      <context>
        <read>.claude/skills/knowledge-sync/SKILL.md</read>
        <info>Currently hardcodes docs/troubleshooting.md, docs/conventions.md, docs/architecture.md, docs/testing.md in the target docs table. Generalize: staged entries already have "target: docs/file.md" embedded, so the skill just needs to follow those. Remove the hardcoded table, keep the format but note targets come from staging entries.</info>
      </context>
      <action>
        - Remove the hardcoded "Target Docs" table
        - Note that each staging entry specifies its own target via "→ target: docs/file.md"
        - Add fallback: if no target specified in entry, ask user
      </action>
      <verification>
        - No hardcoded project-specific paths in SKILL.md (except MEMORY.md discovery which is already generic)
        - Skill still describes promote workflow clearly
      </verification>
    </task>

    <task id="6">
      <description>Generalize review skill for cross-project portability</description>
      <agent>coding</agent>
      <context>
        <read>.claude/skills/review/SKILL.md</read>
        <info>Line 28 hardcodes "Read docs/conventions.md". Generalize: discover conventions file from CLAUDE.md docs table or check for docs/conventions.md as default. If not found, skip convention checking and review against general best practices only.</info>
      </context>
      <action>
        - Replace "Read docs/conventions.md" with discovery: "Look for project conventions file (check CLAUDE.md docs table, or docs/conventions.md). If found, review against it. If not, review against general best practices."
        - Keep all other review logic identical
      </action>
      <verification>
        - No hardcoded project-specific paths in SKILL.md
        - Skill describes how to discover conventions
        - Review checklist unchanged
      </verification>
    </task>
  </wave>

  <wave id="3" mode="parallel">
    <task id="7">
      <description>Update coding agent to discover commands from docs/overview.md</description>
      <agent>coding</agent>
      <context>
        <read>.claude/agents/coding.md</read>
        <info>Lines 24-27 hardcode composer test, php-cs-fixer, phpstan commands. Replace with: "Read docs/overview.md (or project CLAUDE.md) for available commands." The overview already has a commands section.</info>
      </context>
      <action>
        - Replace the hardcoded "Project Commands" section with: "Read docs/overview.md for project commands. If not available, check CLAUDE.md."
        - Keep the dangerouslyDisableSandbox constraint (that's a Claude Code concern, not project-specific... actually it IS project-specific for SQLite)
      </action>
      <verification>
        - coding.md has no hardcoded composer/phpunit/php-cs-fixer/phpstan commands
        - References docs/overview.md for command discovery
      </verification>
    </task>

    <task id="8">
      <description>Update execute-plan agent to discover commands from docs/overview.md</description>
      <agent>coding</agent>
      <context>
        <read>.claude/agents/execute-plan.md</read>
        <info>Lines 33-36 hardcode same commands as coding agent. Same fix: discover from docs. Also lines 53-54 hardcode HEREDOC and sandbox constraints that are project-specific — keep those since this agent stays local.</info>
      </context>
      <action>
        - Replace the hardcoded "Project Commands" section with: "Read docs/overview.md for project commands. If not available, check CLAUDE.md."
      </action>
      <verification>
        - execute-plan.md has no hardcoded composer/phpunit/php-cs-fixer/phpstan commands
        - References docs/overview.md for command discovery
        - Project-specific constraints (sandbox, HEREDOC) still present
      </verification>
    </task>
  </wave>

  <wave id="4" mode="sequential">
    <task id="9">
      <description>Scaffold ~/.claude/workflow/ as plugin repo with existing global components</description>
      <agent>coding</agent>
      <context>
        <read>/Users/nori/.claude/skills/board/SKILL.md</read>
        <read>/Users/nori/.claude/skills/pr-prep/SKILL.md</read>
        <read>/Users/nori/.claude/skills/agent-expert/SKILL.md</read>
        <read>/Users/nori/.claude/skills/agent-researcher/SKILL.md</read>
        <read>/Users/nori/.claude/agents/web.md</read>
        <read>/Users/nori/.claude/hooks/task-complete-ntfy.sh</read>
        <read>/Users/nori/.claude/hooks/test-failure-ntfy.sh</read>
        <info>
          Create ~/.claude/workflow/ with this structure:
          - .claude-plugin/plugin.json (name: nori-workflow, description, author)
          - skills/ — move board/, pr-prep/, agent-expert/, agent-researcher/ here
          - agents/ — move web.md here
          - hooks/ — create hooks.json wiring for task-complete-ntfy.sh and test-failure-ntfy.sh, move scripts here
          - git init the repo

          Plugin.json format: {"name": "nori-workflow", "description": "Personal workflow: orchestration skills, agents, and notification hooks", "author": {"name": "nori"}}

          Hooks.json format: {"description": "...", "hooks": {"PostToolUse": [{"matcher": "TaskUpdate", "hooks": [{"type": "command", "command": "bash ${CLAUDE_PLUGIN_ROOT}/hooks/task-complete-ntfy.sh", "async": true}]}, {"matcher": "Bash", "hooks": [{"type": "command", "command": "bash ${CLAUDE_PLUGIN_ROOT}/hooks/test-failure-ntfy.sh", "async": true}]}]}}

          After moving files, remove the originals from ~/.claude/skills/, ~/.claude/agents/, ~/.claude/hooks/

          IMPORTANT: The agent-expert skill has a references.md file at ~/.claude/skills/agent-expert/references.md — move this too.
          Also check for any other supporting files in each skill directory (like plan/templates.md pattern).
        </info>
      </context>
      <action>
        - Create ~/.claude/workflow/.claude-plugin/plugin.json
        - Move ~/.claude/skills/board/ to ~/.claude/workflow/skills/board/
        - Move ~/.claude/skills/pr-prep/ to ~/.claude/workflow/skills/pr-prep/
        - Move ~/.claude/skills/agent-expert/ to ~/.claude/workflow/skills/agent-expert/
        - Move ~/.claude/skills/agent-researcher/ to ~/.claude/workflow/skills/agent-researcher/
        - Move ~/.claude/agents/web.md to ~/.claude/workflow/agents/web.md
        - Create ~/.claude/workflow/hooks/hooks.json with PostToolUse wiring
        - Move ~/.claude/hooks/task-complete-ntfy.sh to ~/.claude/workflow/hooks/
        - Move ~/.claude/hooks/test-failure-ntfy.sh to ~/.claude/workflow/hooks/
        - Remove the now-empty ~/.claude/skills/, ~/.claude/agents/, ~/.claude/hooks/ dirs
        - git init ~/.claude/workflow/
        - Create .gitignore (ignore *.log, .DS_Store)
      </action>
      <verification>
        - ~/.claude/workflow/.claude-plugin/plugin.json exists and is valid JSON
        - All skill SKILL.md files exist under ~/.claude/workflow/skills/
        - web.md exists under ~/.claude/workflow/agents/
        - hooks.json exists and is valid JSON
        - Hook scripts exist under ~/.claude/workflow/hooks/
        - Old locations are cleaned up
        - git repo initialized
      </verification>
    </task>

    <task id="10">
      <description>Create setup/ dir with templates and install script</description>
      <agent>coding</agent>
      <context>
        <read>/Users/nori/.claude/CLAUDE.md</read>
        <read>/Users/nori/.claude/settings.json</read>
        <info>
          Create ~/.claude/workflow/setup/ with:
          1. CLAUDE.md.template — copy of current ~/.claude/CLAUDE.md principles
          2. settings.json.template — copy of current ~/.claude/settings.json but with the PreToolUse phpunit hook removed (moved to local in task 2), and PostToolUse hooks removed (now in plugin hooks.json). Keep: permissions, model, enabledPlugins (add nori-workflow), sandbox, PermissionRequest hook, Stop ntfy hook.
          3. install.sh — script that:
             - Copies CLAUDE.md.template to ~/.claude/CLAUDE.md (with backup)
             - Copies settings.json.template to ~/.claude/settings.json (with backup)
             - Registers the plugin path if not already in enabledPlugins
             - Reports what was done
        </info>
      </context>
      <action>
        - Create ~/.claude/workflow/setup/CLAUDE.md.template
        - Create ~/.claude/workflow/setup/settings.json.template
        - Create ~/.claude/workflow/setup/install.sh (executable)
      </action>
      <verification>
        - All three files exist in setup/
        - install.sh is executable
        - settings.json.template is valid JSON
        - settings.json.template does not contain hooks that moved to plugin hooks.json
      </verification>
    </task>
  </wave>

  <wave id="5" mode="sequential">
    <task id="11">
      <description>Move generalized capture, knowledge-sync, review skills into the plugin</description>
      <agent>coding</agent>
      <context>
        <read>.claude/skills/capture/SKILL.md</read>
        <read>.claude/skills/knowledge-sync/SKILL.md</read>
        <read>.claude/skills/review/SKILL.md</read>
        <info>After waves 2 and 4 complete, these three skills are generalized and the plugin repo exists. Move them from the local project .claude/skills/ into ~/.claude/workflow/skills/. Delete from local.</info>
      </context>
      <action>
        - Move .claude/skills/capture/ to ~/.claude/workflow/skills/capture/
        - Move .claude/skills/knowledge-sync/ to ~/.claude/workflow/skills/knowledge-sync/
        - Move .claude/skills/review/ to ~/.claude/workflow/skills/review/
        - Remove the now-empty directories from local .claude/skills/
      </action>
      <verification>
        - capture/SKILL.md exists at ~/.claude/workflow/skills/capture/SKILL.md
        - knowledge-sync/SKILL.md exists at ~/.claude/workflow/skills/knowledge-sync/SKILL.md
        - review/SKILL.md exists at ~/.claude/workflow/skills/review/SKILL.md
        - Local .claude/skills/ only contains orchestrate/, plan/, test/
      </verification>
    </task>

    <task id="12">
      <description>Update CLAUDE.md routing tables and MEMORY.md pointers</description>
      <agent>coding</agent>
      <context>
        <read>CLAUDE.md</read>
        <read>/Users/nori/.claude/CLAUDE.md</read>
        <info>
          Update both CLAUDE.md files to reflect new locations:
          - Local CLAUDE.md: update "User-global skills" section to note they're in nori-workflow plugin. Update Skills list to only show orchestrate, plan, test as local. Add capture, knowledge-sync, review to the user-global list.
          - Also find the project MEMORY.md and update the Pointers section to reflect new skill locations.

          MEMORY.md path: ~/.claude/projects/-Users-nori-Projects-ntnu-vektor-v1-monolith/memory/MEMORY.md
        </info>
      </context>
      <action>
        - Edit local CLAUDE.md Skills &amp; Agents section to reflect: local skills = orchestrate, plan, test; plugin skills = board, pr-prep, agent-expert, agent-researcher, capture, knowledge-sync, review
        - Edit MEMORY.md Pointers section to note plugin location
      </action>
      <verification>
        - CLAUDE.md accurately reflects which skills are local vs plugin
        - MEMORY.md pointers updated
      </verification>
    </task>
  </wave>

</plan>

<execution-strategy>
  Wave 1: parallel — 3 independent fixes (hook path, move hook, remove memory)
  Wave 2: sequential — each skill generalization is independent but small; sequential to avoid merge conflicts in similar files
  Wave 3: parallel — 2 independent agent updates
  Wave 4: sequential — plugin scaffold must exist before templates reference it
  Wave 5: sequential — skills must be generalized (wave 2) and plugin must exist (wave 4) before moving
</execution-strategy>
