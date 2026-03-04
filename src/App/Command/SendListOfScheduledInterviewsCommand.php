<?php

namespace App\Command;

use App\Entity\Department;
use App\Entity\Interview;
use App\Service\InterviewManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SendListOfScheduledInterviewsCommand extends Command
{
    private InterviewManager $interviewManager;
    private EntityManagerInterface $em;

    public function __construct(InterviewManager $interviewManager, EntityManagerInterface $em)
    {
        $this->interviewManager = $interviewManager;
        $this->em = $em;
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('app:send_interview_lists')
            ->setDescription('Sends a list of scheduled interview to each interviewer');
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

        return Command::SUCCESS;
    }
}
