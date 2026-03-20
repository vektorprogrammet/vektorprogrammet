<?php

namespace App\Controller;

use App\Entity\Repository\DepartmentRepository;
use App\Shared\Repository\SemesterRepository;
use App\Entity\Repository\TeamRepository;
use App\Event\ApplicationCreatedEvent;
use App\Form\Type\ApplicationExistingUserType;
use App\Service\ApplicationAdmission;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ExistingUserAdmissionController extends BaseController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly TeamRepository $teamRepo,
        private readonly ApplicationAdmission $applicationAdmission,
        private readonly EventDispatcherInterface $eventDispatcher,
        DepartmentRepository $departmentRepo,
        SemesterRepository $semesterRepo,
    ) {
        parent::__construct($departmentRepo, $semesterRepo);
    }

    /**
     * @return RedirectResponse|Response|null
     *
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    #[Route('/eksisterendeopptak', name: 'admission_existing_user', methods: ['GET', 'POST'])]
    public function showAction(Request $request)
    {
        $user = $this->getUser();
        $admissionManager = $this->applicationAdmission;
        if ($res = $admissionManager->renderErrorPage($user)) {
            return $res;
        }

        $department = $user->getDepartment();
        $teams = $this->teamRepo->findActiveByDepartment($department);

        $application = $admissionManager->createApplicationForExistingAssistant($user);

        $form = $this->createForm(ApplicationExistingUserType::class, $application, [
            'validation_groups' => ['admission_existing'],
            'teams' => $teams,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($application);
            $this->em->flush();

            $this->eventDispatcher->dispatch(new ApplicationCreatedEvent($application), ApplicationCreatedEvent::NAME);
            $this->addFlash('success', 'Soknad mottatt!');

            return $this->redirectToRoute('my_page');
        }

        $semester = $this->getCurrentSemester();

        return $this->render('admission/existingUser.html.twig', [
            'form' => $form->createView(),
            'department' => $user->getDepartment(),
            'semester' => $semester,
            'user' => $user,
        ]);
    }
}
