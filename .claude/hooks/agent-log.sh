#!/bin/bash
# SubagentStop hook: append structured log entry
DATA="$CLAUDE_HOOK_EVENT_DATA"
AGENT_NAME=$(echo "$DATA" | jq -r '.agent_name // "unknown"')
AGENT_ID=$(echo "$DATA" | jq -r '.agent_id // "unknown"')
STOP_REASON=$(echo "$DATA" | jq -r '.stop_reason // "unknown"')
TIMESTAMP=$(date -u '+%Y-%m-%dT%H:%M:%SZ')

LOG_FILE="$CLAUDE_PROJECT_DIR/.planning/agent-log.md"

echo "| $TIMESTAMP | $AGENT_NAME | $AGENT_ID | $STOP_REASON |" >> "$LOG_FILE"
