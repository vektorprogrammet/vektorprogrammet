#!/bin/bash
# Stop hook: remind agent to sync knowledge before ending session.
# Checks if MEMORY.md has staged insights pending promotion.
# Returns block decision if entries exist, no-op otherwise.

# Read event data from stdin
INPUT=$(cat)

# Prevent infinite loops — if this hook already fired, let the agent stop
STOP_HOOK_ACTIVE=$(echo "$INPUT" | jq -r '.stop_hook_active // false')
if [ "$STOP_HOOK_ACTIVE" = "true" ]; then
  exit 0
fi

# Derive MEMORY.md path from project directory
PROJECT_DIR=$(echo "$INPUT" | jq -r '.cwd // ""')
if [ -z "$PROJECT_DIR" ]; then
  PROJECT_DIR=$(pwd)
fi
ENCODED=$(echo "$PROJECT_DIR" | sed 's|/|-|g')
MEMORY_FILE="$HOME/.claude/projects/$ENCODED/memory/MEMORY.md"

# If no MEMORY.md exists, nothing to do
if [ ! -f "$MEMORY_FILE" ]; then
  exit 0
fi

# Extract staging section and check for actual entries
STAGING=$(sed -n '/^## Staging/,/^## /p' "$MEMORY_FILE")

# If staging section has the _Empty placeholder, nothing to promote
if echo "$STAGING" | grep -q "_Empty" 2>/dev/null; then
  exit 0
fi

# Count bullet entries in staging section
STAGED_COUNT=$(echo "$STAGING" | grep -c '^- ' 2>/dev/null)
if [ "$STAGED_COUNT" -eq 0 ] 2>/dev/null; then
  exit 0
fi

# Staging has entries — block stop and remind agent
jq -n --arg count "$STAGED_COUNT" '{
  decision: "block",
  reason: ("MEMORY.md has " + $count + " staged insight(s) pending promotion to docs/. Run /knowledge-sync to promote them before ending, or clear the staging section if they are not worth keeping.")
}'
