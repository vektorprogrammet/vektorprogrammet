# Controller DI Analysis

Generated 2026-02-10. Use this to plan Phase 4 batch migrations.

## Controllers With No DI Needs (3)
- AboutVektorController
- FileBrowserController
- ParentsController
- TeacherController

## Controllers Needing Only ManagerRegistry (12)
- AdmissionPeriodController (AdmissionPeriod repo)
- AssistantSchedulingController (AdmissionPeriod, SchoolCapacity, Application repos)
- ChangeLogController (ChangeLogItem repo)
- DepartmentController (getManager())
- FieldOfStudyController (FieldOfStudy repo)
- InterviewSchemaController (InterviewSchema repo)
- MailingListController (User repo)
- ParticipantHistoryController (TeamMembership, AssistantHistory repos)
- PositionController (Position repo)
- SchoolCapacityController (getManager())
- SemesterController (Semester repo)
- SignatureController (Signature repo)
- SocialEventController (getManager(), SocialEvent repo)
- SubstituteController (AdmissionPeriod, Application repos)
- TeamController (Team repo)

## Service String ID → Interface Mapping

| `$this->get('...')` | Constructor type | Used by # controllers |
|---------------------|------------------|-----------------------|
| `'event_dispatcher'` | `EventDispatcherInterface` | 13 |
| `'knp_paginator'` | `PaginatorInterface` | 3 |
| `'kernel'` | `KernelInterface` | 2 (FrontEnd, Profile) |
| `'security.password_hasher'` | `UserPasswordHasherInterface` | 1 (Sso) |
| `'security.token_storage'` | `TokenStorageInterface` | 2 (Profile, Api/Account) |
| `'security.authentication_utils'` | `AuthenticationUtils` | 1 (Security) |
| `'security.authorization_checker'` | `AuthorizationCheckerInterface` | 1 (Security) |
| `'request_stack'` | `RequestStack` | 3 (PasswordReset, Profile, SurveyPopup) |
| `'form.factory'` | `FormFactoryInterface` | 1 (Assistant) |

## App Service Usage (by frequency)

| Service | # Controllers |
|---------|---------------|
| FileUploader | 6 (ArticleAdmin, Certificate, ProfilePhoto, Receipt, Sponsors, +) |
| RoleManager | 5 (ExecutiveBoard, Interview, Profile, Receipt, SurveyPopup) |
| GeoLocation | 4 (Assistant, BoardAndTeam, Contact, Home) |
| LogService | 4 (ArticleAdmin, AssistantHistory, GitHub, PasswordReset) |
| EventDispatcherInterface | 13 |
| AccessControlService | 2 (AccessRule, Survey) |
| ApplicationAdmission | 2 (Assistant, ExistingUserAdmission) |
| InterviewManager | 1 (Interview) |
| SurveyManager | 1 (Survey, but 6 calls) |
| PasswordManager | 1 (PasswordReset, 2 calls) |
| Sorter | 2 (Receipt, Widget) |
| AdmissionStatistics | 1 (Widget) |
| SlugMaker | 1 (ArticleAdmin) |
| SlackMessenger | 1 (Feedback) |
| SurveyNotifier | 1 (SurveyNotifier, 2 calls) |
| UserRegistration | 1 (UserAdmin) |
| ApplicationManager | 1 (User) |
| RoleExtension | 2 (StaticContent, User) |
| ContentModeManager | 0 (only in subscribed services) |
| SbsData | 1 (ControlPanel) |
| UserGroupCollectionManager | 1 (UserGroupCollection) |
| ReversedRoleHierarchy | 2 (AccessRule, Interview) |
| AdmissionNotifier | 1 (AdmissionSubscriber) |
| InterviewCounter | 1 (AdmissionAdmin) |

## Special Cases

### Api/PartyController (extends AbstractFOSRestController)
- Needs: ManagerRegistry only (Application, Semester, AdmissionPeriod repos)
- No parent::__construct() — AbstractFOSRestController has no constructor
- No getDepartment/getSemester helpers available

### Api/AccountController (extends BaseController)
- Needs: ManagerRegistry, TokenStorageInterface, RequestStack

## Suggested Batch Assignment

**Batch 1** (14 controllers, highest complexity):
AdmissionAdminController, InterviewController, SchoolAdminController, TeamAdminController,
SurveyController, ProfileController, ReceiptController, HomeController, WidgetController,
ArticleAdminController, AccessRuleController, PasswordResetController, ExecutiveBoardController,
AssistantController

**Batch 2** (14 controllers):
UserAdminController, StandController(?), CertificateController, ContactController,
SponsorsController, SocialEventController, SubstituteController, ChangeLogController,
DepartmentController, MailingListController, SemesterController, AdmissionPeriodController,
SchoolCapacityController, FeedbackController

**Batch 3** (14 controllers):
UserController, TeamApplicationController, SurveyNotifierController, BoardAndTeamController,
AdmissionSubscriberController, ArticleController, AssistantSchedulingController,
UserGroupCollectionController, ParticipantHistoryController, AssistantHistoryController,
InterviewSchemaController, ProfilePhotoController, PositionController, TeamInterestController

**Batch 4** (remaining ~18):
SurveyPopupController, ControlPanelController, ExistingUserAdmissionController,
TeamController, SecurityController, SsoController, GitHubController, SignatureController,
StaticContentController, FieldOfStudyController, FrontEndController,
FileBrowserController, AboutVektorController, TeacherController, ParentsController,
Api/AccountController, Api/PartyController (special)

## Migration Pattern

```php
// Standard controller extending BaseController
class FooController extends BaseController {
    public function __construct(
        private FooRepository $fooRepo,
        private EntityManagerInterface $em,
        private EventDispatcherInterface $dispatcher,
        DepartmentRepository $departmentRepo,
        SemesterRepository $semesterRepo,
    ) {
        parent::__construct(null, $departmentRepo, $semesterRepo);
    }
}
```

Note: Pass `null` for ManagerRegistry in parent since child uses injected repos directly.
Or better: inject ManagerRegistry and pass to parent for the bridge period.
