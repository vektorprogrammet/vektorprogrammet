<?php
/**
 * Created by IntelliJ IDEA.
 * User: kristoffer
 * Date: 02.01.17
 * Time: 23:33.
 */

namespace AppBundle\Service;

use AppBundle\Entity\Semester;
use DateTime;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class SbsData
{
    private $step;
    private $interviewedAssistantsCount;
    private $assignedInterviewsCount;
    private $totalAssistantsCount;
    private $totalApplicationsCount;
    private $admissionTimeLeft;
    private $timeToAdmissionStart;
    private $positionsCount;

    private $em;
    private $tokenStorage;

    /**
     * SbsData constructor.
     *
     * @param EntityManager         $em
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(EntityManager $em, TokenStorageInterface $tokenStorage)
    {
        $this->step = 0;
        $this->interviewedAssistantsCount = 0;
        $this->assignedInterviewsCount = 0;
        $this->totalAssistantsCount = 0;
        $this->totalApplicationsCount = 0;
        $this->admissionTimeLeft = 0;
        $this->timeToAdmissionStart = 0;
        $this->positionsCount = 0;

        $this->em = $em;
        $this->tokenStorage = $tokenStorage;
        $this->updateData();
    }

    public function updateData()
    {
        $department = $this->tokenStorage->getToken()->getUser()->getDepartment();
        $semester = $this->em->getRepository('AppBundle:Semester')->findCurrentSemesterByDepartment($department);

        if ($semester === null) {
            return;
        }

        $applicationRepo = $this->em->getRepository('AppBundle:Application');
        $assistantHistoryRepo = $this->em->getRepository('AppBundle:AssistantHistory');

        $this->interviewedAssistantsCount = count($applicationRepo->findInterviewedApplicants($department, $semester));
        $this->assignedInterviewsCount = count($applicationRepo->findAssignedApplicants($department, $semester));
        $this->totalApplicationsCount = count($applicationRepo->findBy(array('semester' => $semester)));

        $this->totalAssistantsCount = count($assistantHistoryRepo->findAssistantHistoriesByDepartment($department, $semester));
        $assistantHistories = $assistantHistoryRepo->findAssistantHistoriesByDepartment($department, $semester);

        $this->positionsCount = $this->countPositions($assistantHistories, $this->totalAssistantsCount);

        $this->step = $this->determineCurrentStep($semester, $this->interviewedAssistantsCount, $this->assignedInterviewsCount, $this->totalAssistantsCount);

        if ($this->step >= 1 && $this->step < 2) {
            $this->timeToAdmissionStart = intval(ceil(($semester->getAdmissionStartDate()->getTimestamp() - (new \DateTime())->getTimestamp()) / 3600));
        } elseif ($this->step >= 2 && $this->step < 3) {
            $this->admissionTimeLeft = intval(ceil(($semester->getAdmissionEndDate()->getTimestamp() - (new \DateTime())->getTimestamp()) / 3600));
        }
    }

    private function countPositions(array $assistantHistories, int $totalAssistantsCount): int
    {
        $positionsCount = $totalAssistantsCount;
        foreach ($assistantHistories as $assistant) {
            if ($assistant->getBolk() === 'Bolk 1, Bolk 2') {
                ++$positionsCount;
            }
        }

        return $positionsCount;
    }

    private function determineCurrentStep(Semester $semester, $interviewedAssistantsCount, $assignedInterviewsCount, $totalAssistantsCount): int
    {
        $today = new DateTime('now');

        // Step 1 Before Admission
        if ($today < $semester->getAdmissionStartDate() && $today > $semester->getSemesterStartDate()) {
            return 1 + ($today->format('U') - $semester->getSemesterStartDate()->format('U')) / ($semester->getAdmissionStartDate()->format('U') - $semester->getSemesterStartDate()->format('U'));
        }

        // Step 2 Admission has started
        if ($today < $semester->getAdmissionEndDate() && $today > $semester->getAdmissionStartDate()) {
            return 2 + ($today->format('U') - $semester->getAdmissionStartDate()->format('U')) / ($semester->getAdmissionEndDate()->format('U') - $semester->getAdmissionStartDate()->format('U'));
        }

        // Step 3 Interviewing
        // No interviews are assigned yet
        if ($assignedInterviewsCount == 0 && $interviewedAssistantsCount == 0) {
            return 3;
        } // There are interviews left to conduct
        elseif ($assignedInterviewsCount > 0) {
            return 3 + $interviewedAssistantsCount / ($assignedInterviewsCount + $interviewedAssistantsCount);
        }

        // Step 4 Distribute to schools
        // All interviews are conducted, but no one has been accepted yet
        if ($totalAssistantsCount == 0) {
            return 4;
        }

        // Step 5 Operating phase
        if ($today < $semester->getSemesterEndDate() && $today > $semester->getAdmissionEndDate()) {
            return 5 + ($today->format('U') - $semester->getAdmissionEndDate()->format('U')) / ($semester->getSemesterEndDate()->format('U') - $semester->getAdmissionEndDate()->format('U'));
        }

        // Something is wrong
        return -1;
    }

    /**
     * @return int
     */
    public function getStep(): int
    {
        return $this->step;
    }

    /**
     * @return int
     */
    public function getInterviewedAssistantsCount(): int
    {
        return $this->interviewedAssistantsCount;
    }

    /**
     * @return int
     */
    public function getAssignedInterviewsCount(): int
    {
        return $this->assignedInterviewsCount;
    }

    /**
     * @return int
     */
    public function getTotalAssistantsCount(): int
    {
        return $this->totalAssistantsCount;
    }

    /**
     * @return int
     */
    public function getTotalApplicationsCount(): int
    {
        return $this->totalApplicationsCount;
    }

    /**
     * @return int
     */
    public function getAdmissionTimeLeft(): int
    {
        return $this->admissionTimeLeft;
    }

    /**
     * @return int
     */
    public function getTimeToAdmissionStart(): int
    {
        return $this->timeToAdmissionStart;
    }

    /**
     * @return int
     */
    public function getPositionsCount(): int
    {
        return $this->positionsCount;
    }

    /**
     * @return int
     */
    public function getTotalInterviewsCount(): int
    {
        return $this->assignedInterviewsCount + $this->interviewedAssistantsCount;
    }
}
