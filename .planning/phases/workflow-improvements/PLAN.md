<plan id="workflow-01">

  <task id="1">
    <description>Archive completed phase plans — move completed sprint files to .planning/archive/</description>
    <files>.planning/phases/06-symfony-4.4-to-5.4/, .planning/phases/07-symfony-5.4-to-6.4/, .planning/archive/</files>
    <action>
      - Create .planning/archive/phases/ directory
      - Move 06-symfony-4.4-to-5.4/ and 07-symfony-5.4-to-6.4/ into archive
      - Agents globbing .planning/phases/ will no longer see stale plans
    </action>
    <verification>
      - .planning/phases/ only has workflow-improvements/
      - .planning/archive/phases/ has old sprint dirs
    </verification>
  </task>

  <task id="2">
    <description>Update ROADMAP.md to reflect actual state — Milestone 2 COMPLETE</description>
    <files>.planning/ROADMAP.md</files>
    <action>
      - Mark Milestone 2 as COMPLETE, remove stale "CURRENT"/"IN PROGRESS" markers
      - Mark Sprint 7b under Milestone 2
      - Update Milestone 3 sprint 8 to reflect actual deferred work
      - Keep concise — this is a summary, STATE.md has details
    </action>
    <verification>
      - No stale status markers
      - Matches STATE.md progress checklist
    </verification>
  </task>

  <task id="3">
    <description>Clean up STATE.md — remove duplicated test command, update timestamp</description>
    <files>.planning/STATE.md</files>
    <action>
      - Remove "Test Command" section at bottom (duplicates docs/testing.md)
      - Add pointer: "See docs/testing.md for test commands and workflow"
      - Keep everything else (progress, sprint details, decisions, deferred)
    </action>
    <verification>
      - No test commands in STATE.md
      - Link to docs/testing.md present
    </verification>
  </task>

  <task id="4">
    <description>Add file→test mapping to docs/testing.md</description>
    <files>docs/testing.md</files>
    <action>
      - Add "File → Test Mapping" section after "Recommended Workflow"
      - Map source controller files to their test class + suite
      - Map entity files to entity unit tests
      - Map service files to service tests
      - Map template files to availability suite
      - Include --filter command for each
    </action>
    <verification>
      - Every controller test class has a source mapping
      - Table is scannable — agents can grep for controller name
    </verification>
  </task>

  <task id="5">
    <description>Create docs/architecture.md with details moved from CLAUDE.md</description>
    <files>docs/architecture.md</files>
    <action>
      - Move architecture details from CLAUDE.md into new file
      - Include: BaseController pattern, security roles, User entity, Role entity, service patterns
      - Add section on getDoctrine()/get() bridge pattern and deferred DI migration
      - Reference from CLAUDE.md
    </action>
    <verification>
      - All architecture bullets from CLAUDE.md are in docs/architecture.md
      - File is self-contained — readable without other context
    </verification>
  </task>

  <task id="6">
    <description>Slim down CLAUDE.md to ~20 lines — pointers only</description>
    <files>CLAUDE.md</files>
    <action>
      - Keep: project one-liner, sprint status, pointer to docs/testing.md, pointer to docs/architecture.md
      - Keep: top 3 critical gotchas (sandbox, known failures, HEREDOC)
      - Keep: workflow section (start/plan/dev/end)
      - Remove: test commands (→ docs/testing.md), architecture bullets (→ docs/architecture.md), test credentials (→ docs/testing.md)
    </action>
    <verification>
      - Under 25 lines
      - All removed info accessible via linked docs
    </verification>
  </task>

  <task id="7">
    <description>Restructure MEMORY.md — categorized error patterns, eliminate duplication</description>
    <files>~/.claude/projects/-Users-nori-Projects-ntnu-vektor-v1-monolith/memory/MEMORY.md</files>
    <action>
      - Remove "Current State" section (→ .planning/STATE.md)
      - Remove "Test Command" section (→ docs/testing.md)
      - Remove "Deferred to Sprint 8" (→ STATE.md)
      - Keep "GitHub Reference" and "Workflow Rules"
      - Restructure "Lessons Learned" into categorized sections:
        - Sandbox / Environment (sandbox blocks, HEREDOC, bootstrap noise)
        - Symfony 6 API Changes (getDoctrine, security, session, mailer, etc)
        - Twig 3 Changes (for...if, spaceless, blocks in if, form prototypes, asset null)
        - PHPUnit / Testing (mock return types, WebTestCase singleton, createClient)
        - Infrastructure (SQLite race, lazy DB queries, ContainerAwareCommand)
      - Each entry should include the error message or symptom as a keyword
    </action>
    <verification>
      - No duplication with STATE.md, CLAUDE.md, or docs/testing.md
      - Under 200 lines (truncation limit)
      - Every lesson is in exactly one category
    </verification>
  </task>

  <task id="8">
    <description>Add pre-commit verification hook + test failure notification hook to ~/.claude/settings.json</description>
    <files>~/.claude/settings.json, ~/.claude/hooks/test-failure-ntfy.sh</files>
    <action>
      - Add PreToolUse hook on Bash matching "git commit" — outputs reminder to stderr
      - Add PostToolUse hook on Bash — detects phpunit in command + non-zero exit, sends ntfy
      - Create ~/.claude/hooks/test-failure-ntfy.sh script
      - Both hooks use async: true to not block workflow
      - Pre-commit hook uses blocking (not async) to ensure agent sees the message
    </action>
    <verification>
      - settings.json is valid JSON
      - Hook scripts are executable
      - Pre-commit reminder appears in agent context before commit executes
    </verification>
  </task>

</plan>

<execution-strategy>
  <wave id="1" mode="parallel" note="Independent file moves and updates">
    <tasks>1, 2, 3</tasks>
    <notes>Archive stale plans, fix ROADMAP, clean STATE.md. No dependencies.</notes>
  </wave>
  <wave id="2" mode="parallel" note="New docs + test mapping">
    <tasks>4, 5</tasks>
    <notes>Add file→test mapping, create architecture.md. Independent of each other.</notes>
  </wave>
  <wave id="3" mode="sequential" note="Depends on tasks 4+5 being done">
    <tasks>6, 7</tasks>
    <notes>Slim CLAUDE.md and restructure MEMORY.md — must happen after docs exist to point to.</notes>
  </wave>
  <wave id="4" mode="sequential" note="Hooks — independent but last to avoid disrupting workflow">
    <tasks>8</tasks>
    <notes>Add hooks to settings.json. Done last since it changes agent behavior for all future sessions.</notes>
  </wave>
</execution-strategy>
