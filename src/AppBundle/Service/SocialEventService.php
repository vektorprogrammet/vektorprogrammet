<?php


namespace AppBundle\Service;

use AppBundle\Entity\AssistantHistory;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use phpDocumentor\Reflection\Types\This;

class SocialEventService
{
    private $socialEventRepository;
    private $currentSemester;
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
        $this->socialEventRepository = $em->getRepository('AppBundle:SocialEvent');
        $this->currentSemester = $em->getRepository('AppBundle:Semester')->findCurrentSemester();
    }

    # This method was created with generating working days for assistants as events to show on the new assistant dash.
    #
    public function getNextEventsByUser(User $user)
    {
        $department = $user->getDepartment();
        $eventsThisSemester = $this->socialEventRepository->findSocialEventsBySemesterAndDepartment($this->currentSemester, $department);

        $activeAssistantHistories = $this->em->getRepository('AppBundle:AssistantHistory')->findActiveAssistantHistoriesByUser($user);
        $test = array();
        foreach ($activeAssistantHistories as $history) {
            $test[$history->getDay()] = ['number_of_days' => $history->getWorkdays(), 'bolk' => $history->getBolk()];
        }
    }
}
