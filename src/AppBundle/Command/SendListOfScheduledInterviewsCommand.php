<?php

namespace AppBundle\Command;

use AppBundle\Service\InterviewManager;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SendListOfScheduledInterviewsCommand extends ContainerAwareCommand
{
    /**
     * @var InterviewManager
     */
    private $interviewManager;

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('app:send_interview_lists')
            ->setDescription('Sends a list of scheduled interview to each interviewer');
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->interviewManager = $this->getContainer()->get(InterviewManager::class);
        $this->em = $this->getContainer()->get('doctrine.orm.default_entity_manager');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $departments = $this->em->getRepository('AppBundle:Department')->findActive();
        foreach ($departments as $department) {
            $admissionPeriod = $department->getCurrentAdmissionPeriod();
            if (!$admissionPeriod) {
                continue;
            }

            $semester = $admissionPeriod->getSemester();
            $interviewersInDepartment = $this->em->getRepository('AppBundle:Interview')->findInterviewersInSemester($semester);
            foreach ($interviewersInDepartment as $interviewer) {
                $this->interviewManager->sendInterviewScheduleToInterviewer($interviewer);
            }
        }
    }
}
