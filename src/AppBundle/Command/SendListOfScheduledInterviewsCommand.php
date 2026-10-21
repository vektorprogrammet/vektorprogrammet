<?php

namespace AppBundle\Command;

use AppBundle\Entity\Department;
use AppBundle\Entity\Interview;
use AppBundle\Service\InterviewManager;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SendListOfScheduledInterviewsCommand extends ContainerCommand
{
    /**
     * @var InterviewManager
     */
    private $interviewManager;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this
            ->setName('app:send_interview_lists')
            ->setDescription('Sends a list of scheduled interview to each interviewer');
    }

    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this->interviewManager = $this->getContainer()->get(InterviewManager::class);
        $this->em = $this->getContainer()->get('doctrine.orm.default_entity_manager');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $departments = $this->em->getRepository(Department::class)->findActive();
        foreach ($departments as $department) {
            $admissionPeriod = $department->getCurrentAdmissionPeriod();
            if (!$admissionPeriod) {
                continue;
            }

            $semester = $admissionPeriod->getSemester();
            $interviewersInDepartment = $this->em->getRepository(Interview::class)->findInterviewersInSemester($semester);
            foreach ($interviewersInDepartment as $interviewer) {
                $this->interviewManager->sendInterviewScheduleToInterviewer($interviewer);
            }
        }
        return self::SUCCESS;
    }
}
