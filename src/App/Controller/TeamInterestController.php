<?php

namespace App\Controller;

use App\Entity\Repository\DepartmentRepository;
use App\Entity\Repository\SemesterRepository;
use App\Entity\Semester;
use App\Event\TeamInterestCreatedEvent;
use App\Entity\Department;
use App\Entity\TeamInterest;
use App\Form\Type\TeamInterestType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class TeamInterestController extends BaseController
{
    public function __construct(
        private EntityManagerInterface $em,
        private EventDispatcherInterface $eventDispatcher,
        DepartmentRepository $departmentRepo,
        SemesterRepository $semesterRepo,
    ) {
        parent::__construct($departmentRepo, $semesterRepo);
    }

    /**
     * @Route(name="team_interest_form",
     *     path="/teaminteresse/{id}",
     *     requirements={"id"="\d+"},
     *     methods={"GET", "POST"}
     * )
     *
     * @param Department|NULL $department
     * @param Request $request
     *
     * @return RedirectResponse|Response
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function showTeamInterestFormAction(Department $department, Request $request)
    {
        $semester = $this->getCurrentSemester();
        if ($semester === null) {
            throw new BadRequestHttpException('No current semester');
        }

        $teamInterest = new TeamInterest();
        $teamInterest->setSemester($semester);
        $teamInterest->setDepartment($department);
        $form = $this->createForm(TeamInterestType::class, $teamInterest, array(
            'department' => $department,
        ));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($teamInterest);
            $this->em->flush();

            $this->eventDispatcher->dispatch(new TeamInterestCreatedEvent($teamInterest), TeamInterestCreatedEvent::NAME);

            return $this->redirectToRoute('team_interest_form', array(
                'id' => $department->getId(),
            ));
        }

        return $this->render('team_interest/team_interest.html.twig', array(
            'form' => $form->createView(),
        ));
    }
}
