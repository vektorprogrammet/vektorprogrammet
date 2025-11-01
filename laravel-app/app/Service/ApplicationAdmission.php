<?php

namespace App\Service;

use App\Models\AdmissionPeriod;
use App\Models\Application;
use App\Models\Department;
use App\Models\Interview;
use App\Models\Role;
use App\Models\User;
use App\Repository\Contract\AdmissionPeriodRepositoryInterface;
use App\Repository\Contract\ApplicationRepositoryInterface;
use App\Repository\Contract\DepartmentRepositoryInterface;
use App\Repository\Contract\InterviewRepositoryInterface;
use App\Repository\Contract\RoleRepositoryInterface;
use App\Repository\Contract\UserRepositoryInterface;
use App\Role\Roles;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Twig\Environment;
use App\Service\Contract\ApplicationAdmissionInterface;
use App\Service\Contract\LoginManagerInterface;

class ApplicationAdmission implements ApplicationAdmissionInterface
{
    private AdmissionPeriodRepositoryInterface $admissionPeriodRepository;
    private ApplicationRepositoryInterface $applicationRepository;
    private DepartmentRepositoryInterface $departmentRepository;
    private InterviewRepositoryInterface $interviewRepository;
    private RoleRepositoryInterface $roleRepository;
    private UserRepositoryInterface $userRepository;
    private Environment $twig;
    private LoginManagerInterface $loginManager;

    /**
     * AdmissionManager constructor.
     *
     * @param AdmissionPeriodRepositoryInterface $admissionPeriodRepository
     * @param ApplicationRepositoryInterface $applicationRepository
     * @param DepartmentRepositoryInterface $departmentRepository
     * @param InterviewRepositoryInterface $interviewRepository
     * @param RoleRepositoryInterface $roleRepository
     * @param UserRepositoryInterface $userRepository
     * @param Environment $twig
     * @param LoginManagerInterface $loginManager
     */
    public function __construct(
        AdmissionPeriodRepositoryInterface $admissionPeriodRepository,
        ApplicationRepositoryInterface $applicationRepository,
        DepartmentRepositoryInterface $departmentRepository,
        InterviewRepositoryInterface $interviewRepository,
        RoleRepositoryInterface $roleRepository,
        UserRepositoryInterface $userRepository,
        Environment $twig,
        LoginManagerInterface $loginManager
    ) {
        $this->admissionPeriodRepository = $admissionPeriodRepository;
        $this->applicationRepository = $applicationRepository;
        $this->departmentRepository = $departmentRepository;
        $this->interviewRepository = $interviewRepository;
        $this->roleRepository = $roleRepository;
        $this->userRepository = $userRepository;
        $this->twig = $twig;
        $this->loginManager = $loginManager;
    }

    public function createApplicationForExistingAssistant(User $user): Application
    {
        $department = $user->fieldOfStudy->department ?? null;
        if ($department === null) {
            throw new \RuntimeException('User has no department');
        }
        
        $admissionPeriod = $this->admissionPeriodRepository->findOneWithActiveAdmissionByDepartment($department);

        $application = $this->applicationRepository->findByUserInAdmissionPeriod($user, $admissionPeriod);
        if ($application === null) {
            $application = new Application();
        }

        $lastInterview = $this->interviewRepository->findLatestInterviewByUser($user);

        $application->user_id = $user->id;
        $application->admission_period_id = $admissionPeriod->id;
        $application->previous_participation = true;
        if ($lastInterview !== null) {
            $application->interview_id = $lastInterview->id;
        }

        return $application;
    }

    public function userHasAlreadyApplied(User $user): bool
    {
        $fieldOfStudy = $user->fieldOfStudy;
        if ($fieldOfStudy === null) {
            /* User has no field of study, and hence no department, so we
            cannot know if he/she has already applied in the current semester,
            as this depends on the department. */
            return false;
        }
        $department = $fieldOfStudy->department ?? null;
        if ($department === null) {
            return false;
        }
        $admissionPeriod = $this->admissionPeriodRepository->findOneWithActiveAdmissionByDepartment($department);
        if ($admissionPeriod === null) {
            return false;
        }
        return $this->userHasAlreadyAppliedInAdmissionPeriod($user, $admissionPeriod);
    }

    public function userHasAlreadyAppliedInAdmissionPeriod(User $user, AdmissionPeriod $admissionPeriod): bool
    {
        $existingApplications = $this->applicationRepository->findByEmailInAdmissionPeriod($user->email, $admissionPeriod);

        return count($existingApplications) > 0;
    }


    public function setCorrectUser(Application $application): void
    {
        //Check if email belongs to an existing account and use that account
        $userEmail = $application->user->email ?? null;
        if ($userEmail === null) {
            return;
        }
        
        $user = $this->userRepository->findUserByEmail($userEmail);
        if ($user !== null) {
            $application->user_id = $user->id;
        }

        $applicationUser = $application->user;
        $userRoles = $applicationUser->roles ?? collect([]);
        if ($userRoles->isEmpty()) {
            $role = $this->roleRepository->findByRoleName(Roles::ASSISTANT);
            $applicationUser->roles()->sync([$role->id]);
            $applicationUser->save();
        }
    }

    public function getExistingAssistantLoginMessage(): string
    {
        return $this->twig->render('login/existing_assistant_login_message.html.twig');
    }

    public function getDepartment(Request $request): Department
    {
        $departmentIdQuery = $request->get('id');
        $departmentShortNameQuery = $request->get('shortName');
        $department = null;

        if ($departmentIdQuery !== null) {
            // Note: Direct find() might need to be added to DepartmentRepository if not available
            $department = \App\Models\Department::find($departmentIdQuery);
        } elseif ($departmentShortNameQuery !== null) {
            $department = $this->departmentRepository->findDepartmentByShortName($departmentShortNameQuery);
        }

        if ($department === null) {
            throw new NotFoundHttpException('Department not found');
        }

        return $department;
    }

    public function renderErrorPage(User $user = null): ?Response
    {
        $content = null;

        if ($user === null) {
            $message = $this->getExistingAssistantLoginMessage();

            $content = $this->loginManager->renderLogin($message, 'admission_existing_user');
        } elseif (!$user->hasBeenAssistant()) {
            $content = $this->twig->render('error/no_assistanthistory.html.twig', array('user' => $user));
        } else {
            $department = $user->fieldOfStudy->department ?? null;
            if ($department === null) {
                $content = $this->twig->render(':error:no_active_admission.html.twig');
            } else {
                $admissionPeriod = $this->admissionPeriodRepository->findOneWithActiveAdmissionByDepartment($department);

                if ($admissionPeriod === null) {
                    $content = $this->twig->render(':error:no_active_admission.html.twig');
                }
            }
        }

        if ($content !== null) {
            $response = new Response();
            $response->setContent($content);

            return $response;
        }

        return null;
    }
}
