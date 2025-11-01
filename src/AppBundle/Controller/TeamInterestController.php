<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Department;
use AppBundle\Entity\Semester;
use AppBundle\Entity\TeamInterest;
use AppBundle\Event\TeamInterestCreatedEvent;
use AppBundle\Form\Type\TeamInterestType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;

class TeamInterestController extends BaseController
{
    private $entityManager;
    private $eventDispatcher;

    /**
     * @param EntityManagerInterface $entityManager
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->entityManager = $entityManager;
        $this->eventDispatcher = $eventDispatcher;
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
            $this->entityManager->persist($teamInterest);
            $this->entityManager->flush();

            $this->eventDispatcher->dispatch(TeamInterestCreatedEvent::NAME, new TeamInterestCreatedEvent($teamInterest));

            return $this->redirectToRoute('team_interest_form', array(
                'id' => $department->getId(),
            ));
        }

        return $this->render(':team_interest:team_interest.html.twig', array(
            'form' => $form->createView(),
        ));
    }
}
