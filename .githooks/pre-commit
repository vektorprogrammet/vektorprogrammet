#!/bin/bash
# Pre-commit hook installed by havn.
# Auto-detects tooling or reads overrides from .havn.json.
# Three concerns: formatting (always), code quality (always), tests+changesets (main only).

# --- Config: .havn.json overrides or auto-detect ---

json_field() {
  local file="$1" field="$2"
  if [ -f "$file" ] && command -v jq &>/dev/null; then
    jq -r "$field // empty" "$file" 2>/dev/null
  fi
}

detect_runner() {
  if [ -f "turbo.json" ] && command -v turbo &>/dev/null; then
    echo "turbo"
  elif command -v bun &>/dev/null; then
    echo "bun"
  elif command -v npm &>/dev/null; then
    echo "npm"
  fi
}

has_script() {
  local script="$1"
  [ -f "package.json" ] && grep -q "\"$script\"" package.json 2>/dev/null
}

runner=$(detect_runner)

# Format command
CMD_FORMAT=$(json_field .havn.json '.precommit.format')
if [ -z "$CMD_FORMAT" ]; then
  if [ "$runner" = "turbo" ] && has_script format; then
    CMD_FORMAT="turbo format"
  elif has_script format; then
    CMD_FORMAT="$runner run format"
  fi
fi

# Quality command (lint + build)
CMD_QUALITY=$(json_field .havn.json '.precommit.quality')
if [ -z "$CMD_QUALITY" ]; then
  if [ "$runner" = "turbo" ]; then
    parts=""
    has_script lint && parts="turbo lint"
    has_script build && parts="${parts:+$parts && }turbo build"
    CMD_QUALITY="$parts"
  else
    parts=""
    has_script lint && parts="$runner run lint"
    has_script build && parts="${parts:+$parts && }$runner run build"
    CMD_QUALITY="$parts"
  fi
fi

# Test command
CMD_TEST=$(json_field .havn.json '.precommit.test')
if [ -z "$CMD_TEST" ]; then
  if has_script test; then
    if [ "$runner" = "turbo" ]; then
      CMD_TEST="turbo test"
    else
      CMD_TEST="$runner run test"
    fi
  fi
fi

# Main branch name
MAIN_BRANCH=$(json_field .havn.json '.precommit.main')
[ -z "$MAIN_BRANCH" ] && MAIN_BRANCH="main"

branch=$(git rev-parse --abbrev-ref HEAD)

# --- Formatting: auto-fix and stage (all branches) ---
if [ -n "$CMD_FORMAT" ]; then
  echo "→ Format: $CMD_FORMAT"
  eval "$CMD_FORMAT" || exit 1
  git add -u
fi

# --- Code quality: lint + build (all branches) ---
if [ -n "$CMD_QUALITY" ]; then
  echo "→ Quality: $CMD_QUALITY"
  eval "$CMD_QUALITY" || exit 1
fi

# --- Main-only: tests + changeset verification ---
if [ "$branch" = "$MAIN_BRANCH" ]; then
  if [ -n "$CMD_TEST" ]; then
    echo "→ Test: $CMD_TEST"
    eval "$CMD_TEST" || exit 1
  fi

  if [ -d ".changeset" ]; then
    echo "→ Changeset status"
    bunx changeset status --since=HEAD~1 || {
      echo "Missing changeset. Run 'bunx changeset' to add one."
      exit 1
    }
  fi
fi
