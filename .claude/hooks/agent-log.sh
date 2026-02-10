#!/bin/bash
# SubagentStop hook: append structured log entry
DATA="$CLAUDE_HOOK_EVENT_DATA"

if ! command -v jq &>/dev/null; then
  echo "| $(date -u '+%Y-%m-%dT%H:%M:%SZ') | jq-missing | - | - |" >> "$CLAUDE_PROJECT_DIR/.planning/agent-log.md"
  exit 0
fi

AGENT_TYPE=$(echo "$DATA" | jq -r '.agent_type // "unknown"')
AGENT_ID=$(echo "$DATA" | jq -r '.agent_id // "unknown"')
TIMESTAMP=$(date -u '+%Y-%m-%dT%H:%M:%SZ')

LOG_FILE="$CLAUDE_PROJECT_DIR/.planning/agent-log.md"

echo "| $TIMESTAMP | $AGENT_TYPE | $AGENT_ID |" >> "$LOG_FILE"
