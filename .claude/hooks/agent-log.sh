#!/bin/bash
# SubagentStop hook: append structured log entry
# Event data arrives via stdin as JSON
TIMESTAMP=$(date -u '+%Y-%m-%dT%H:%M:%SZ')
LOG_FILE="$CLAUDE_PROJECT_DIR/.planning/agent-log.md"
DATA=$(cat)

if ! command -v jq &>/dev/null; then
  echo "| $TIMESTAMP | jq-missing | - |" >> "$LOG_FILE"
  exit 0
fi

AGENT_TYPE=$(echo "$DATA" | jq -r '.agent_type // "unknown"')
AGENT_ID=$(echo "$DATA" | jq -r '.agent_id // "unknown"')

echo "| $TIMESTAMP | $AGENT_TYPE | $AGENT_ID |" >> "$LOG_FILE"
