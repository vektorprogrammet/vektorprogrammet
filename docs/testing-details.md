# Testing Details

Extended reference. Quick commands: [`docs/testing.md`](testing.md)

## Test Suites

| Suite          | Tests | Time  | What it covers                                                         |
| -------------- | ----- | ----- | ---------------------------------------------------------------------- |
| `unit`         | 183   | <1s   | Entity unit tests, services, SMS, form types, utils                    |
| `controller`   | 131   | ~110s | Functional tests per controller (login, submit forms, check responses) |
| `availability` | 182   | ~67s  | Smoke tests per role (5 files: Public, Assistant, TeamMember, TeamLeader, Admin) |
| `api`*         | 23    | ~52s  | API Platform endpoint + contract tests (ContentApiTest, ApplicationApiTest) |

*API tests are in `tests/AppBundle/Api/` and run as part of the default suite (no separate testsuite config).

## File → Test Mapping

Use `--filter=TestClassName` to run only the relevant test when editing a source file.

| Source file pattern                          | Test class                                                 | Suite        | Time |
| -------------------------------------------- | ---------------------------------------------------------- | ------------ | ---- |
| `Controller/AdmissionAdminController`        | AdmissionAdminControllerTest                               | controller   | ~12s |
| `Controller/InterviewController`             | InterviewControllerTest                                    | controller   | ~12s |
| `Controller/ReceiptController`               | ReceiptControllerTest                                      | controller   | ~10s |
| `Controller/SchoolAdminController`           | SchoolAdminControllerTest                                  | controller   | ~8s  |
| `Controller/TeamAdminController`             | TeamAdminControllerTest                                    | controller   | ~7s  |
| `Controller/AccessRuleController`            | AccessRuleControllerTest                                   | controller   | ~7s  |
| `Controller/SurveyPopupController`           | SurveyPopUpControllerTest                                  | controller   | ~6s  |
| `Controller/MailingListController`           | MailingListControllerTest                                  | controller   | ~5s  |
| `Controller/ExecutiveBoardController`        | ExecutiveBoardControllerTest                               | controller   | ~5s  |
| `Controller/UserAdminController`             | UserAdminControllerTest                                    | controller   | ~3s  |
| `Controller/ProfileController`               | ProfileControllerTest                                      | controller   | ~3s  |
| `Controller/SecurityController`              | SecurityControllerTest                                     | controller   | ~2s  |
| `Controller/PasswordResetController`         | PasswordResetControllerTest                                | controller   | ~2s  |
| `Controller/DepartmentController`            | DepartmentControllerTest                                   | controller   | ~2s  |
| `Controller/FeedbackController`              | FeedbackControllerTest                                     | controller   | ~2s  |
| `Controller/HomeController`                  | HomeControllerTest                                         | controller   | ~1s  |
| `Controller/SubstituteController`            | SubstituteControllerTest                                   | controller   | ~1s  |
| `Controller/ArticleController`               | ArticleControllerTest                                      | controller   | ~1s  |
| `Controller/ArticleAdminController`          | ArticleAdminControllerTest                                 | controller   | ~1s  |
| `Controller/AssistantController`             | AssistantControllerTest                                    | controller   | ~1s  |
| `Controller/BoardAndTeamController`          | BoardAndTeamControllerTest                                 | controller   | ~1s  |
| `Controller/SemesterController`              | SemesterControllerTest                                     | controller   | ~1s  |
| `Controller/SocialEventController`           | SocialEventControllerTest                                  | controller   | ~1s  |
| `Controller/TeamApplicationController`       | TeamApplicationControllerTest                              | controller   | ~1s  |
| `Controller/TeamInterestController`          | TeamInterestControllerTest                                 | controller   | ~1s  |
| `Controller/ChangeLogController`             | ChangeLogControllerTest                                    | controller   | ~1s  |
| `Controller/InfoMeetingController`           | InfoMeetingControllerTest                                  | controller   | ~1s  |
| `Controller/AboutVektorController`           | AboutVektorControllerTest                                  | controller   | ~1s  |
| `Controller/SchoolsController`               | SchoolsControllerTest                                      | controller   | ~1s  |
| `Controller/StudentsController`              | StudentsControllerTest                                     | controller   | ~1s  |
| `Controller/ExistingUserAdmissionController` | ExistingUserAdmissionControllerTest                        | controller   | ~1s  |
| `Controller/ParticipantHistoryController`    | ParticipantHistoryControllerTest                           | controller   | ~1s  |
| `Entity/*`                                   | \*EntityUnitTest (matching name)                           | unit         | <1s  |
| `Service/Sorter`                             | SorterTest                                                 | unit         | <1s  |
| `Service/RoleManager`                        | RoleManagerTest                                            | unit         | <1s  |
| Symfony `SluggerInterface` (was SlugMaker)   | SluggerTest                                                | unit         | <1s  |
| `Service/CompanyEmailMaker`                  | CompanyEmailMakerTest                                      | unit         | <1s  |
| `Service/GeoLocation`                        | GeoLocationTest                                            | unit         | <1s  |
| `Service/AccessControl`                      | AccessControlTest                                          | unit         | <1s  |
| `Sms/GatewayApi`                             | GatewayApiTest                                             | unit         | <1s  |
| `Form/Type/*`                                | CreateDepartmentTest, CreatePositionTest, CreateSchoolTest | unit         | <1s  |
| `templates/**/*.twig`                        | *PageAvailabilityTest (per role)                           | availability | ~67s |

**No test?** Controllers without a dedicated test are covered by the availability smoke tests (5 role-based files).

## Test Database

**In-memory SQLite** with `dama/doctrine-test-bundle` for transaction-based test isolation.

The bootstrap (`tests/bootstrap.php`) creates the schema and loads fixtures once per PHPUnit process:

1. Enables DAMA's `StaticDriver::setKeepStaticConnections(true)` (retains the in-memory DB across kernel reboots)
2. Boots kernel, creates schema via `doctrine:schema:create`
3. Loads fixtures via `doctrine:fixtures:load`
4. Commits DAMA's auto-started transaction (`StaticDriver::commit()`)

DAMA wraps each test in a savepoint and rolls back after — no manual DB restoration needed.

**Parallel mode**: Each ParaTest worker is a separate process with its own in-memory DB. DAMA handles isolation within each worker automatically.

If you get stale cache errors:

```bash
rm -rf var/cache/test/
```

## Test Credentials

| Username     | Password | Role                  |
| ------------ | -------- | --------------------- |
| `assistent`  | `1234`   | ROLE_USER (assistant) |
| `teammember` | `1234`   | ROLE_TEAM_MEMBER      |
| `teamleader` | `1234`   | ROLE_TEAM_LEADER      |
| `admin`      | `1234`   | ROLE_ADMIN            |

## Test Architecture

- `BaseWebTestCase` provides `createAssistantClient()`, `createTeamLeaderClient()`, etc.
- Clients are cached as static properties per class (Sf6 singleton kernel)
- `BaseKernelTestCase` for service-level tests without HTTP
- Availability tests split into 5 role-based files, each with data providers for URL lists

## Timing Profile (top 10 slowest)

| Test Class                   | Tests | Time  | %   |
| ---------------------------- | ----- | ----- | --- |
| Availability tests (5 files) | 182   | 66.5s | 37% |
| AdmissionAdminControllerTest | 13    | 12.1s | 7%  |
| InterviewControllerTest      | 9     | 11.8s | 7%  |
| ReceiptControllerTest        | 10    | 10.2s | 6%  |
| SchoolAdminControllerTest    | 8     | 8.2s  | 5%  |
| TeamAdminControllerTest      | 10    | 7.0s  | 4%  |
| AccessRuleControllerTest     | 4     | 6.6s  | 4%  |
| SurveyPopUpControllerTest    | 4     | 5.6s  | 3%  |
| MailingListControllerTest    | 2     | 5.1s  | 3%  |
| ExecutiveBoardControllerTest | 6     | 4.8s  | 3%  |
| _Unit tests (all 183)_       | 183   | <1s   | <1% |

## Parallel Testing (ParaTest)

ParaTest runs tests across 4 workers using `WrapperRunner`. Each worker gets its own in-memory SQLite DB (DAMA static connections are per-process).

- Use `WrapperRunner` (not default runner) — preserves static state within a worker, fewer double-boot errors
- `BaseWebTestCase::createClient()` has a catch-retry pattern for Sf6.4 double-boot (`LogicException`)
- Do NOT close DBAL connections in `tearDown` — breaks DAMA's static connection

## Environment

- **PHP 8.5**: minimum required version (see `composer.json`)
- **SQLite**: in-memory (`:memory:`) — no files on disk
- **DAMA**: `dama/doctrine-test-bundle` v8 for transaction-based test isolation
- **Sandbox**: tests must run with sandbox disabled
- **Memory**: 256M limit (set by `composer test`; default 128M is insufficient)
