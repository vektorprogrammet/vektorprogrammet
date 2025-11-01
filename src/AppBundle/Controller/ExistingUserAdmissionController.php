<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Semester;
use AppBundle\Entity\Team;
use AppBundle\Event\ApplicationCreatedEvent;
use AppBundle\Form\Type\ApplicationExistingUserType;
use AppBundle\Repository\Contract\TeamRepositoryInterface;
use AppBundle\Service\Contract\ApplicationAdmissionInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ExistingUserAdmissionController extends BaseController
{
    private $entityManager;
    private $applicationAdmission;
    private $teamRepository;
    private $eventDispatcher;

    /**
     * @param EntityManagerInterface $entityManager
     * @param ApplicationAdmissionInterface $applicationAdmission
     * @param TeamRepositoryInterface $teamRepository
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        ApplicationAdmissionInterface $applicationAdmission,
        TeamRepositoryInterface $teamRepository,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->entityManager = $entityManager;
        $this->applicationAdmission = $applicationAdmission;
        $this->teamRepository = $teamRepository;
        $this->eventDispatcher = $eventDispatcher;
    }
    /**
     * @Route("/eksisterendeopptak",
     *     name="admission_existing_user",
     *     methods={"GET", "POST"}
     * )
     *
     * @param Request $request
     *
     * @return null|RedirectResponse|Response
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function showAction(Request $request)
    {
        $user = $this->getUser();
        if ($res = $this->applicationAdmission->renderErrorPage($user)) {
            return $res;
        }

        $department = $user->getDepartment();
        $teams = $this->teamRepository->findActiveByDepartment($department);

        $application = $this->applicationAdmission->createApplicationForExistingAssistant($user);

        $form = $this->createForm(ApplicationExistingUserType::class, $application, array(
            'validation_groups' => array('admission_existing'),
            'teams' => $teams,
        ));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($application);
            $this->entityManager->flush();

            $this->eventDispatcher->dispatch(ApplicationCreatedEvent::NAME, new ApplicationCreatedEvent($application));
            $this->addFlash("success", "Søknad mottatt!");

            return $this->redirectToRoute('my_page');
        }

        $semester = $this->getCurrentSemester();

        return $this->render(':admission:existingUser.html.twig', array(
            'form' => $form->createView(),
            'department' => $user->getDepartment(),
            'semester' => $semester,
            'user' => $user,
        ));
    }
}
