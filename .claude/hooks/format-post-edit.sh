#!/bin/bash
# Auto-format PHP files after Edit/Write tool use
# Triggered as PostToolUse hook — non-blocking, best-effort

DATA=$(cat)
FILE_PATH=$(echo "$DATA" | jq -r '.tool_input.file_path // ""')

# Only run for .php files
[[ "$FILE_PATH" != *.php ]] && exit 0

# Only run if php-cs-fixer config exists in the project
[[ ! -f ".php-cs-fixer.dist.php" ]] && exit 0

# Only run if the binary exists
[[ ! -f "bin/php-cs-fixer" && ! -f "vendor/bin/php-cs-fixer" ]] && exit 0

# Run php-cs-fixer on the specific file
if [[ -f "bin/php-cs-fixer" ]]; then
    bin/php-cs-fixer fix --quiet "$FILE_PATH" 2>/dev/null
elif [[ -f "vendor/bin/php-cs-fixer" ]]; then
    vendor/bin/php-cs-fixer fix --quiet "$FILE_PATH" 2>/dev/null
fi

# Always exit 0 — formatting is best-effort, never block the agent
exit 0
