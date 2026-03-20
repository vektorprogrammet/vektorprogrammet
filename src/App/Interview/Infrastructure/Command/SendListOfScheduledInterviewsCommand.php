<?php

namespace App\Interview\Infrastructure\Command;

use App\Organization\Infrastructure\Entity\Department;
use App\Interview\Infrastructure\Entity\Interview;
use App\Interview\Infrastructure\InterviewManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SendListOfScheduledInterviewsCommand extends Command
{
    public function __construct(private readonly InterviewManager $interviewManager, private readonly EntityManagerInterface $em)
    {
        parent::__construct();
    }

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
            if ($admissionPeriod === null) {
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
