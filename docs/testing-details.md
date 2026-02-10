# Testing Details

Extended reference. Quick commands: [`docs/testing.md`](testing.md)

## Test Suites

| Suite | Tests | Time | What it covers |
|-------|-------|------|----------------|
| `unit` | 183 | <1s | Entity unit tests, services, SMS, form types, utils |
| `controller` | 131 | ~110s | Functional tests per controller (login, submit forms, check responses) |
| `availability` | 182 | ~67s | Smoke test hitting every URL with different auth levels |

## File → Test Mapping

Use `--filter=TestClassName` to run only the relevant test when editing a source file.

| Source file pattern | Test class | Suite | Time |
|---|---|---|---|
| `Controller/AdmissionAdminController` | AdmissionAdminControllerTest | controller | ~12s |
| `Controller/InterviewController` | InterviewControllerTest | controller | ~12s |
| `Controller/ReceiptController` | ReceiptControllerTest | controller | ~10s |
| `Controller/SchoolAdminController` | SchoolAdminControllerTest | controller | ~8s |
| `Controller/TeamAdminController` | TeamAdminControllerTest | controller | ~7s |
| `Controller/AccessRuleController` | AccessRuleControllerTest | controller | ~7s |
| `Controller/SurveyPopupController` | SurveyPopUpControllerTest | controller | ~6s |
| `Controller/MailingListController` | MailingListControllerTest | controller | ~5s |
| `Controller/ExecutiveBoardController` | ExecutiveBoardControllerTest | controller | ~5s |
| `Controller/UserAdminController` | UserAdminControllerTest | controller | ~3s |
| `Controller/ProfileController` | ProfileControllerTest | controller | ~3s |
| `Controller/SecurityController` | SecurityControllerTest | controller | ~2s |
| `Controller/PasswordResetController` | PasswordResetControllerTest | controller | ~2s |
| `Controller/DepartmentController` | DepartmentControllerTest | controller | ~2s |
| `Controller/FeedbackController` | FeedbackControllerTest | controller | ~2s |
| `Controller/HomeController` | HomeControllerTest | controller | ~1s |
| `Controller/SubstituteController` | SubstituteControllerTest | controller | ~1s |
| `Controller/ArticleController` | ArticleControllerTest | controller | ~1s |
| `Controller/ArticleAdminController` | ArticleAdminControllerTest | controller | ~1s |
| `Controller/AssistantController` | AssistantControllerTest | controller | ~1s |
| `Controller/BoardAndTeamController` | BoardAndTeamControllerTest | controller | ~1s |
| `Controller/SemesterController` | SemesterControllerTest | controller | ~1s |
| `Controller/SocialEventController` | SocialEventControllerTest | controller | ~1s |
| `Controller/TeamApplicationController` | TeamApplicationControllerTest | controller | ~1s |
| `Controller/TeamInterestController` | TeamInterestControllerTest | controller | ~1s |
| `Controller/ChangeLogController` | ChangeLogControllerTest | controller | ~1s |
| `Controller/InfoMeetingController` | InfoMeetingControllerTest | controller | ~1s |
| `Controller/AboutVektorController` | AboutVektorControllerTest | controller | ~1s |
| `Controller/SchoolsController` | SchoolsControllerTest | controller | ~1s |
| `Controller/StudentsController` | StudentsControllerTest | controller | ~1s |
| `Controller/ExistingUserAdmissionController` | ExistingUserAdmissionControllerTest | controller | ~1s |
| `Controller/ParticipantHistoryController` | ParticipantHistoryControllerTest | controller | ~1s |
| `Entity/*` | *EntityUnitTest (matching name) | unit | <1s |
| `Service/Sorter` | SorterTest | unit | <1s |
| `Service/RoleManager` | RoleManagerTest | unit | <1s |
| `Service/SlugMaker` | SlugMakerTest | unit | <1s |
| `Service/CompanyEmailMaker` | CompanyEmailMakerTest | unit | <1s |
| `Service/GeoLocation` | GeoLocationTest | unit | <1s |
| `Service/AccessControl` | AccessControlTest | unit | <1s |
| `Sms/GatewayApi` | GatewayApiTest | unit | <1s |
| `Form/Type/*` | CreateDepartmentTest, CreatePositionTest, CreateSchoolTest | unit | <1s |
| `templates/**/*.twig` | AvailabilityFunctionalTest | availability | ~67s |

**No test?** Controllers without a dedicated test are covered by `AvailabilityFunctionalTest` (smoke test).

## Test Database

The bootstrap (`tests/bootstrap.php`) handles DB setup automatically:
1. Deletes `test.db` and `test.db.bk`
2. Creates schema via `doctrine:schema:create`
3. Loads fixtures via `doctrine:fixtures:load`
4. Backs up to `test.db.bk`

Each test's `tearDown()` restores from `test.db.bk`, so tests are isolated.

**Parallel mode**: ParaTest sets `TEST_TOKEN` per worker (1, 2, 3...). The bootstrap creates `test1.db`, `test2.db`, etc. via `TestDataManager::getToken()`. Config resolves via `%env(default::TEST_TOKEN)%`.

If you get stale DB errors, clear everything:
```bash
rm -f var/data/test*.db var/data/test*.db.bk && rm -rf var/cache/test/
```

## Test Credentials

| Username | Password | Role |
|----------|----------|------|
| `assistent` | `1234` | ROLE_USER (assistant) |
| `teammember` | `1234` | ROLE_TEAM_MEMBER |
| `teamleader` | `1234` | ROLE_TEAM_LEADER |
| `admin` | `1234` | ROLE_ADMIN |

## Test Architecture

- `BaseWebTestCase` provides `createAssistantClient()`, `createTeamLeaderClient()`, etc.
- Clients are cached as static properties per class (Sf6 singleton kernel)
- `BaseKernelTestCase` for service-level tests without HTTP
- `AvailabilityFunctionalTest` uses data providers to test URL lists per auth level

## Timing Profile (top 10 slowest)

| Test Class | Tests | Time | % |
|-----------|-------|------|---|
| AvailabilityFunctionalTest | 182 | 66.5s | 37% |
| AdmissionAdminControllerTest | 13 | 12.1s | 7% |
| InterviewControllerTest | 9 | 11.8s | 7% |
| ReceiptControllerTest | 10 | 10.2s | 6% |
| SchoolAdminControllerTest | 8 | 8.2s | 5% |
| TeamAdminControllerTest | 10 | 7.0s | 4% |
| AccessRuleControllerTest | 4 | 6.6s | 4% |
| SurveyPopUpControllerTest | 4 | 5.6s | 3% |
| MailingListControllerTest | 2 | 5.1s | 3% |
| ExecutiveBoardControllerTest | 6 | 4.8s | 3% |
| *Unit tests (all 171)* | 171 | <1s | <1% |
