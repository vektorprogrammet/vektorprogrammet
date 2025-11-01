<?php

namespace App\Providers;

use App\Repository\Contract\AccessRuleRepositoryInterface;
use App\Repository\Contract\AdmissionNotificationRepositoryInterface;
use App\Repository\Contract\AdmissionPeriodRepositoryInterface;
use App\Repository\Contract\AdmissionRepositoryInterface;
use App\Repository\Contract\AdmissionSubscriberRepositoryInterface;
use App\Repository\Contract\ChangeLogItemRepositoryInterface;
use App\Repository\Contract\ApplicationRepositoryInterface;
use App\Repository\Contract\ArticleRepositoryInterface;
use App\Repository\Contract\AssistantHistoryRepositoryInterface;
use App\Repository\Contract\DepartmentRepositoryInterface;
use App\Repository\Contract\ExecutiveBoardMembershipRepositoryInterface;
use App\Repository\Contract\ExecutiveBoardRepositoryInterface;
use App\Repository\Contract\FeedbackRepositoryInterface;
use App\Repository\Contract\FieldOfStudyRepositoryInterface;
use App\Repository\Contract\InterviewRepositoryInterface;
use App\Repository\Contract\PasswordResetRepositoryInterface;
use App\Repository\Contract\ReceiptRepositoryInterface;
use App\Repository\Contract\RoleRepositoryInterface;
use App\Repository\Contract\SchoolRepositoryInterface;
use App\Repository\Contract\SemesterRepositoryInterface;
use App\Repository\Contract\SchoolCapacityRepositoryInterface;
use App\Repository\Contract\SignatureRepositoryInterface;
use App\Repository\Contract\SocialEventRepositoryInterface;
use App\Repository\Contract\StaticContentRepositoryInterface;
use App\Repository\Contract\SurveyNotificationRepositoryInterface;
use App\Repository\Contract\SurveyRepositoryInterface;
use App\Repository\Contract\SurveyTakenRepositoryInterface;
use App\Repository\Contract\TeamApplicationRepositoryInterface;
use App\Repository\Contract\TeamMembershipRepositoryInterface;
use App\Repository\Contract\TeamRepositoryInterface;
use App\Repository\Contract\UnhandledAccessRuleRepositoryInterface;
use App\Repository\Contract\UserRepositoryInterface;
use App\Repository\Eloquent\AccessRuleRepository;
use App\Repository\Eloquent\AdmissionNotificationRepository;
use App\Repository\Eloquent\AdmissionPeriodRepository;
use App\Repository\Eloquent\AdmissionRepository;
use App\Repository\Eloquent\AdmissionSubscriberRepository;
use App\Repository\Eloquent\ChangeLogItemRepository;
use App\Repository\Eloquent\ApplicationRepository;
use App\Repository\Eloquent\ArticleRepository;
use App\Repository\Eloquent\AssistantHistoryRepository;
use App\Repository\Eloquent\DepartmentRepository;
use App\Repository\Eloquent\ExecutiveBoardMembershipRepository;
use App\Repository\Eloquent\ExecutiveBoardRepository;
use App\Repository\Eloquent\FeedbackRepository;
use App\Repository\Eloquent\FieldOfStudyRepository;
use App\Repository\Eloquent\InterviewRepository;
use App\Repository\Eloquent\PasswordResetRepository;
use App\Repository\Eloquent\ReceiptRepository;
use App\Repository\Eloquent\RoleRepository;
use App\Repository\Eloquent\SchoolCapacityRepository;
use App\Repository\Eloquent\SchoolRepository;
use App\Repository\Eloquent\SemesterRepository;
use App\Repository\Eloquent\SignatureRepository;
use App\Repository\Eloquent\SocialEventRepository;
use App\Repository\Eloquent\StaticContentRepository;
use App\Repository\Eloquent\SurveyNotificationRepository;
use App\Repository\Eloquent\SurveyRepository;
use App\Repository\Eloquent\SurveyTakenRepository;
use App\Repository\Eloquent\TeamApplicationRepository;
use App\Repository\Eloquent\TeamMembershipRepository;
use App\Repository\Eloquent\TeamRepository;
use App\Repository\Eloquent\UnhandledAccessRuleRepository;
use App\Repository\Eloquent\UserRepository;
use Illuminate\Support\ServiceProvider;

/**
 * Service Provider for Repository bindings.
 *
 * This provider wires up all repository interfaces to their Eloquent implementations.
 */
class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Core repositories
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(DepartmentRepositoryInterface::class, DepartmentRepository::class);
        $this->app->bind(SemesterRepositoryInterface::class, SemesterRepository::class);
        $this->app->bind(ApplicationRepositoryInterface::class, ApplicationRepository::class);
        $this->app->bind(AdmissionPeriodRepositoryInterface::class, AdmissionPeriodRepository::class);

        // Additional repositories
        $this->app->bind(ArticleRepositoryInterface::class, ArticleRepository::class);
        $this->app->bind(TeamRepositoryInterface::class, TeamRepository::class);
        $this->app->bind(InterviewRepositoryInterface::class, InterviewRepository::class);
        $this->app->bind(RoleRepositoryInterface::class, RoleRepository::class);
        $this->app->bind(ReceiptRepositoryInterface::class, ReceiptRepository::class);
        $this->app->bind(FeedbackRepositoryInterface::class, FeedbackRepository::class);

        // High-priority repositories
        $this->app->bind(AssistantHistoryRepositoryInterface::class, AssistantHistoryRepository::class);
        $this->app->bind(SurveyRepositoryInterface::class, SurveyRepository::class);
        $this->app->bind(SchoolRepositoryInterface::class, SchoolRepository::class);
        $this->app->bind(FieldOfStudyRepositoryInterface::class, FieldOfStudyRepository::class);
        $this->app->bind(TeamMembershipRepositoryInterface::class, TeamMembershipRepository::class);

        // Additional medium-priority repositories
        $this->app->bind(ExecutiveBoardRepositoryInterface::class, ExecutiveBoardRepository::class);
        $this->app->bind(ExecutiveBoardMembershipRepositoryInterface::class, ExecutiveBoardMembershipRepository::class);
        $this->app->bind(TeamApplicationRepositoryInterface::class, TeamApplicationRepository::class);
        $this->app->bind(AdmissionSubscriberRepositoryInterface::class, AdmissionSubscriberRepository::class);
        $this->app->bind(AdmissionNotificationRepositoryInterface::class, AdmissionNotificationRepository::class);
        $this->app->bind(SurveyTakenRepositoryInterface::class, SurveyTakenRepository::class);
        $this->app->bind(SurveyNotificationRepositoryInterface::class, SurveyNotificationRepository::class);
        $this->app->bind(StaticContentRepositoryInterface::class, StaticContentRepository::class);
        $this->app->bind(SocialEventRepositoryInterface::class, SocialEventRepository::class);
        $this->app->bind(PasswordResetRepositoryInterface::class, PasswordResetRepository::class);
        $this->app->bind(SignatureRepositoryInterface::class, SignatureRepository::class);

        // Final remaining repositories
        $this->app->bind(AdmissionRepositoryInterface::class, AdmissionRepository::class);
        $this->app->bind(SchoolCapacityRepositoryInterface::class, SchoolCapacityRepository::class);
        $this->app->bind(ChangeLogItemRepositoryInterface::class, ChangeLogItemRepository::class);
        $this->app->bind(AccessRuleRepositoryInterface::class, AccessRuleRepository::class);
        $this->app->bind(UnhandledAccessRuleRepositoryInterface::class, UnhandledAccessRuleRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}

