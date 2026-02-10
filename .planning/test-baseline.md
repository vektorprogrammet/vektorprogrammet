# Test Baseline

Track test counts after each commit to catch regressions early.

| Date | Commit | Tests | Assertions | Failures | Errors | Time | Notes |
|------|--------|-------|------------|----------|--------|------|-------|
| 2026-02-09 | bfd90f8a | 496 | 1150 | 0 | 0 | 3:08 | Baseline established. All green. |
| 2026-02-10 | ae04e49d | 496 | 1150 | 0 | 0 | 3:03 | Sprint 9: routes + deprecation fixes. Still green. |

## Running Tests

```bash
rm -rf var/cache/tes_/ var/data/test.db var/data/test.db-journal && php -d memory_limit=256M bin/phpunit --no-coverage
```

## Notes
- Clean test DB AND cache dir before runs to avoid stale cache / SQLite disk I/O errors
- Memory limit 256M sufficient (160MB actual peak usage)
- `testShouldSendInfoMeetingNotification` is flaky near midnight (time-of-day dependent)
- If failures appear that weren't in baseline, investigate before committing
