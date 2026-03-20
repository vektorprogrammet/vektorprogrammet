<?php

declare(strict_types=1);

namespace App\Organization\Controller;

use App\Support\Controller\BaseController;
use App\Organization\Infrastructure\Entity\Department;
use App\Organization\Infrastructure\Repository\DepartmentRepository;
use App\Shared\Repository\SemesterRepository;
use App\Organization\Infrastructure\Entity\TeamInterest;
use App\Organization\Domain\Events\TeamInterestCreatedEvent;
use App\Organization\Form\TeamInterestType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TeamInterestController extends BaseController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly EventDispatcherInterface $eventDispatcher,
        DepartmentRepository $departmentRepo,
        SemesterRepository $semesterRepo,
    ) {
        parent::__construct($departmentRepo, $semesterRepo);
    }

    /**
     * @return RedirectResponse|Response
     *
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    #[Route(name: 'team_interest_form', path: '/teaminteresse/{id}', requirements: ['id' => "\d+"], methods: ['GET', 'POST'])]
    public function showTeamInterestFormAction(Department $department, Request $request)
    {
        $semester = $this->getCurrentSemester();

        $teamInterest = new TeamInterest();
        $teamInterest->setSemester($semester);
        $teamInterest->setDepartment($department);
        $form = $this->createForm(TeamInterestType::class, $teamInterest, [
            'department' => $department,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($teamInterest);
            $this->em->flush();

            $this->eventDispatcher->dispatch(new TeamInterestCreatedEvent($teamInterest), TeamInterestCreatedEvent::NAME);

            return $this->redirectToRoute('team_interest_form', [
                'id' => $department->getId(),
            ]);
        }

        return $this->render('team_interest/team_interest.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
