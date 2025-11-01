<?php

namespace App\Providers;

use App\Service\Contract\AccessControlServiceInterface;
use App\Service\Contract\AdmissionAdminServiceInterface;
use App\Service\Contract\AdmissionNotifierInterface;
use App\Service\Contract\AdmissionPeriodManagementServiceInterface;
use App\Service\Contract\AdmissionPeriodValidationServiceInterface;
use App\Service\Contract\AdmissionServiceInterface;
use App\Service\Contract\AdmissionStatisticsInterface;
use App\Service\Contract\ApplicationAdmissionInterface;
use App\Service\Contract\ApplicationDataInterface;
use App\Service\Contract\ApplicationManagerInterface;
use App\Service\Contract\ArticleManagementServiceInterface;
use App\Service\Contract\ArticleServiceInterface;
use App\Service\Contract\AssistantHistoryDataInterface;
use App\Service\Contract\AssistantSchedulingDataServiceInterface;
use App\Service\Contract\BetaRedirecterInterface;
use App\Service\Contract\CertificateServiceInterface;
use App\Service\Contract\CompanyEmailMakerInterface;
use App\Service\Contract\ContentModeManagerInterface;
use App\Service\Contract\DepartmentManagementServiceInterface;
use App\Service\Contract\EmailSenderInterface;
use App\Service\Contract\ExecutiveBoardManagementServiceInterface;
use App\Service\Contract\ExecutiveBoardServiceInterface;
use App\Service\Contract\FeedbackSubmissionServiceInterface;
use App\Service\Contract\FieldOfStudyManagementServiceInterface;
use App\Service\Contract\FileUploaderInterface;
use App\Service\Contract\FilterServiceInterface;
use App\Service\Contract\GeoLocationInterface;
use App\Service\Contract\HomeServiceInterface;
use App\Service\Contract\InterviewCounterInterface;
use App\Service\Contract\InterviewManagerInterface;
use App\Service\Contract\InterviewNotificationManagerInterface;
use App\Service\Contract\InterviewSchedulingServiceInterface;
use App\Service\Contract\LoginManagerInterface;
use App\Service\Contract\LogServiceInterface;
use App\Service\Contract\PartnerServiceInterface;
use App\Service\Contract\PasswordManagerInterface;
use App\Service\Contract\ProfileServiceInterface;
use App\Service\Contract\ReceiptStatisticsServiceInterface;
use App\Service\Contract\RoleManagerInterface;
use App\Service\Contract\SbsDataInterface;
use App\Service\Contract\SchoolManagementServiceInterface;
use App\Service\Contract\SemesterManagementServiceInterface;
use App\Service\Contract\SemesterValidationServiceInterface;
use App\Service\Contract\SlackMailerInterface;
use App\Service\Contract\SlackMessengerInterface;
use App\Service\Contract\SlugMakerInterface;
use App\Service\Contract\SorterInterface;
use App\Service\Contract\SurveyExecutionServiceInterface;
use App\Service\Contract\SurveyManagerInterface;
use App\Service\Contract\SurveyNotifierInterface;
use App\Service\Contract\TeamAdminServiceInterface;
use App\Service\Contract\TeamMembershipServiceInterface;
use App\Service\Contract\UserGroupCollectionManagerInterface;
use App\Service\Contract\UserManagementServiceInterface;
use App\Service\Contract\UserRegistrationInterface;
use App\Service\Contract\UserServiceInterface;
use App\Service\AccessControlService;
use App\Service\AdmissionAdminService;
use App\Service\AdmissionNotifier;
use App\Service\AdmissionPeriodManagementService;
use App\Service\AdmissionPeriodValidationService;
use App\Service\AdmissionService;
use App\Service\AdmissionStatistics;
use App\Service\ApplicationAdmission;
use App\Service\ApplicationData;
use App\Service\ApplicationManager;
use App\Service\ArticleManagementService;
use App\Service\ArticleService;
use App\Service\AssistantHistoryData;
use App\Service\AssistantSchedulingDataService;
use App\Service\BetaRedirecter;
use App\Service\CertificateService;
use App\Service\CompanyEmailMaker;
use App\Service\ContentModeManager;
use App\Service\DepartmentManagementService;
use App\Service\EmailSender;
use App\Service\ExecutiveBoardManagementService;
use App\Service\ExecutiveBoardService;
use App\Service\FeedbackSubmissionService;
use App\Service\FieldOfStudyManagementService;
use App\Service\FileUploader;
use App\Service\FilterService;
use App\Service\GeoLocation;
use App\Service\HomeService;
use App\Service\InterviewCounter;
use App\Service\InterviewManager;
use App\Service\InterviewNotificationManager;
use App\Service\InterviewSchedulingService;
use App\Service\LoginManager;
use App\Service\LogService;
use App\Service\PartnerService;
use App\Service\PasswordManager;
use App\Service\ProfileService;
use App\Service\ReceiptStatisticsService;
use App\Service\RoleManager;
use App\Service\SbsData;
use App\Service\SchoolManagementService;
use App\Service\SemesterManagementService;
use App\Service\SemesterValidationService;
use App\Service\SlackMailer;
use App\Service\SlackMessenger;
use App\Service\SlugMaker;
use App\Service\Sorter;
use App\Service\SurveyExecutionService;
use App\Service\SurveyManager;
use App\Service\SurveyNotifier;
use App\Service\TeamAdminService;
use App\Service\TeamMembershipService;
use App\Service\UserGroupCollectionManager;
use App\Service\UserManagementService;
use App\Service\UserRegistration;
use App\Service\UserService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Bind all service interfaces to their implementations
        $this->app->bind(AccessControlServiceInterface::class, AccessControlService::class);
        $this->app->bind(AdmissionAdminServiceInterface::class, AdmissionAdminService::class);
        $this->app->bind(AdmissionNotifierInterface::class, AdmissionNotifier::class);
        $this->app->bind(AdmissionPeriodManagementServiceInterface::class, AdmissionPeriodManagementService::class);
        $this->app->bind(AdmissionPeriodValidationServiceInterface::class, AdmissionPeriodValidationService::class);
        $this->app->bind(AdmissionServiceInterface::class, AdmissionService::class);
        $this->app->bind(AdmissionStatisticsInterface::class, AdmissionStatistics::class);
        $this->app->bind(ApplicationAdmissionInterface::class, ApplicationAdmission::class);
        $this->app->bind(ApplicationDataInterface::class, ApplicationData::class);
        $this->app->bind(ApplicationManagerInterface::class, ApplicationManager::class);
        $this->app->bind(ArticleManagementServiceInterface::class, ArticleManagementService::class);
        $this->app->bind(ArticleServiceInterface::class, ArticleService::class);
        $this->app->bind(AssistantHistoryDataInterface::class, AssistantHistoryData::class);
        $this->app->bind(AssistantSchedulingDataServiceInterface::class, AssistantSchedulingDataService::class);
        $this->app->bind(BetaRedirecterInterface::class, BetaRedirecter::class);
        $this->app->bind(CertificateServiceInterface::class, CertificateService::class);
        $this->app->bind(CompanyEmailMakerInterface::class, CompanyEmailMaker::class);
        $this->app->bind(ContentModeManagerInterface::class, ContentModeManager::class);
        $this->app->bind(DepartmentManagementServiceInterface::class, DepartmentManagementService::class);
        $this->app->bind(EmailSenderInterface::class, EmailSender::class);
        $this->app->bind(ExecutiveBoardManagementServiceInterface::class, ExecutiveBoardManagementService::class);
        $this->app->bind(ExecutiveBoardServiceInterface::class, ExecutiveBoardService::class);
        $this->app->bind(FeedbackSubmissionServiceInterface::class, FeedbackSubmissionService::class);
        $this->app->bind(FieldOfStudyManagementServiceInterface::class, FieldOfStudyManagementService::class);
        $this->app->bind(FileUploaderInterface::class, FileUploader::class);
        $this->app->bind(FilterServiceInterface::class, FilterService::class);
        $this->app->bind(GeoLocationInterface::class, GeoLocation::class);
        $this->app->bind(HomeServiceInterface::class, HomeService::class);
        $this->app->bind(InterviewCounterInterface::class, InterviewCounter::class);
        $this->app->bind(InterviewManagerInterface::class, InterviewManager::class);
        $this->app->bind(InterviewNotificationManagerInterface::class, InterviewNotificationManager::class);
        $this->app->bind(InterviewSchedulingServiceInterface::class, InterviewSchedulingService::class);
        $this->app->bind(LoginManagerInterface::class, LoginManager::class);
        $this->app->bind(LogServiceInterface::class, LogService::class);
        $this->app->bind(PartnerServiceInterface::class, PartnerService::class);
        $this->app->bind(PasswordManagerInterface::class, PasswordManager::class);
        $this->app->bind(ProfileServiceInterface::class, ProfileService::class);
        $this->app->bind(ReceiptStatisticsServiceInterface::class, ReceiptStatisticsService::class);
        $this->app->bind(RoleManagerInterface::class, RoleManager::class);
        $this->app->bind(SbsDataInterface::class, SbsData::class);
        $this->app->bind(SchoolManagementServiceInterface::class, SchoolManagementService::class);
        $this->app->bind(SemesterManagementServiceInterface::class, SemesterManagementService::class);
        $this->app->bind(SemesterValidationServiceInterface::class, SemesterValidationService::class);
        $this->app->bind(SlackMailerInterface::class, SlackMailer::class);
        $this->app->bind(SlackMessengerInterface::class, SlackMessenger::class);
        $this->app->bind(SlugMakerInterface::class, SlugMaker::class);
        $this->app->bind(SorterInterface::class, Sorter::class);
        $this->app->bind(SurveyExecutionServiceInterface::class, SurveyExecutionService::class);
        $this->app->bind(SurveyManagerInterface::class, SurveyManager::class);
        $this->app->bind(SurveyNotifierInterface::class, SurveyNotifier::class);
        $this->app->bind(TeamAdminServiceInterface::class, TeamAdminService::class);
        $this->app->bind(TeamMembershipServiceInterface::class, TeamMembershipService::class);
        $this->app->bind(UserGroupCollectionManagerInterface::class, UserGroupCollectionManager::class);
        $this->app->bind(UserManagementServiceInterface::class, UserManagementService::class);
        $this->app->bind(UserRegistrationInterface::class, UserRegistration::class);
        $this->app->bind(UserServiceInterface::class, UserService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
