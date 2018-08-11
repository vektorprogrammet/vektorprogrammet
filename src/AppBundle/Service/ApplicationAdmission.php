<?php

namespace AppBundle\Service;

use AppBundle\Entity\Application;
use AppBundle\Entity\Department;
use AppBundle\Entity\Semester;
use AppBundle\Entity\User;
use AppBundle\Role\Roles;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ApplicationAdmission
{
    private $em;
    private $twig;
    private $loginManager;

    /**
     * AdmissionManager constructor.
     *
     * @param EntityManager     $em
     * @param \Twig_Environment $twig
     * @param LoginManager      $loginManager
     */
    public function __construct(EntityManager $em, \Twig_Environment $twig, LoginManager $loginManager)
    {
        $this->em = $em;
        $this->twig = $twig;
        $this->loginManager = $loginManager;
    }

    public function createApplicationForExistingAssistant(User $user): Application
    {
        $semester = $this->em->getRepository('AppBundle:Semester')->findSemesterWithActiveAdmissionByDepartment($user->getDepartment());

        $application = $this->em->getRepository('AppBundle:Application')->findByUserInSemester($user, $semester);
        if ($application === null) {
            $application = new Application();
        }

        $lastInterview = $this->em->getRepository('AppBundle:Interview')->findLatestInterviewByUser($user);

        $application->setUser($user);
        $application->setSemester($semester);
        $application->setPreviousParticipation(true);
        $application->setInterview($lastInterview);

        return $application;
    }

    public function userHasAlreadyApplied(User $user)
    {
        $fos = $user->getFieldOfStudy();
        if ($fos === null) {
            /* User has no field of study, and hence no department, so we
            cannot know if he/she has already applied in the current semester,
            as this depends on the department. */
            return false;
        }
        $department = $fos->getDepartment();
        $semester = $this->em->getRepository('AppBundle:Semester')
            ->findSemesterWithActiveAdmissionByDepartment($department);
        return $this->userHasAlreadyAppliedInSemester($user, $semester);
    }

    public function userHasAlreadyAppliedInSemester(User $user, Semester $semester)
    {
        $existingApplications = $this->em->getRepository('AppBundle:Application')->findByEmailInSemester($user->getEmail(), $semester);

        return count($existingApplications) > 0;
    }


    public function setCorrectUser(Application $application)
    {
        //Check if email belongs to an existing account and use that account
        $user = $this->em->getRepository('AppBundle:User')->findOneBy(array('email' => $application->getUser()->getEmail()));
        if ($user !== null) {
            $application->setUser($user);
        }

        if (count($application->getUser()->getRoles()) === 0) {
            $role = $this->em->getRepository('AppBundle:Role')->findByRoleName(Roles::ASSISTANT);
            $application->getUser()->addRole($role);
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
            $department = $this->em->getRepository('AppBundle:Department')->find($departmentIdQuery);
        } elseif ($departmentShortNameQuery !== null) {
            $department = $this->em->getRepository('AppBundle:Department')->findDepartmentByShortName($departmentShortNameQuery);
        }

        if ($department === null) {
            throw  new NotFoundHttpException('Department not found');
        }

        return $department;
    }

    public function renderErrorPage(User $user = null)
    {
        $content = null;

        if ($user === null) {
            $message = $this->getExistingAssistantLoginMessage();

            $content = $this->loginManager->renderLogin($message, 'admission_existing_user');
        } elseif (!$user->hasBeenAssistant()) {
            $content = $this->twig->render('error/no_assistanthistory.html.twig', array('user' => $user));
        } else {
            $department = $user->getDepartment();
            $semester = $this->em->getRepository('AppBundle:Semester')->findSemesterWithActiveAdmissionByDepartment($department);

            if ($semester === null) {
                $content = $this->twig->render(':error:no_active_admission.html.twig');
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
