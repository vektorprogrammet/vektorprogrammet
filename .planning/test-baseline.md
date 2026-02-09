# Test Baseline

Track test counts after each commit to catch regressions early.

| Date | Commit | Tests | Assertions | Failures | Errors | Time | Notes |
|------|--------|-------|------------|----------|--------|------|-------|
| 2026-02-09 | bfd90f8a | 496 | 1150 | 0 | 0 | 3:08 | Baseline established. All green. |

## Running Tests

```bash
rm -f var/data/test.db* && php -d memory_limit=512M bin/phpunit --no-coverage
```

## Notes
- Clean test DB before runs to avoid SQLite disk I/O errors
- Memory limit 512M needed (160MB actual usage)
- If failures appear that weren't in baseline, investigate before committing
