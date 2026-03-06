<?php

namespace App\Controller;

use App\Entity\Repository\DepartmentRepository;
use App\Entity\Repository\SemesterRepository;
use App\Entity\Repository\SocialEventRepository;
use App\Entity\SocialEvent;
use App\Form\Type\SocialEventType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SocialEventController extends BaseController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly SocialEventRepository $socialEventRepo,
        DepartmentRepository $departmentRepo,
        SemesterRepository $semesterRepo,
    ) {
        parent::__construct($departmentRepo, $semesterRepo);
    }

    /**
     * @return Response|null
     */
    #[Route('/kontrollpanel/arrangementer', name: 'social_event_show', methods: ['GET'])]
    public function showAction(Request $request)
    {
        $department = $this->getDepartmentOrThrow404($request);
        $semester = $this->getSemesterOrThrow404($request);

        $SocialEventList = $this->socialEventRepo->findSocialEventsBySemesterAndDepartment($semester, $department);

        return $this->render('social_event/social_event_list.twig', [
            'department' => $department,
            'semester' => $semester,
            'SocialEventList' => $SocialEventList,
            'now' => new \DateTime(),
        ]);
    }

    /**
     * @return RedirectResponse|Response
     */
    #[Route('/kontrollpanel/arrangement/opprett', name: 'social_event_create', methods: ['GET', 'POST'])]
    public function createSocialEventAction(Request $request)
    {
        $department = $this->getDepartmentOrThrow404($request);
        $semester = $this->getSemesterOrThrow404($request);
        $socialEvent = new SocialEvent();
        $user = $this->getUser();

        $form = $this->createForm(SocialEventType::class, $socialEvent, [
            'department' => $department,
            'semester' => $semester,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($socialEvent);
            $this->em->flush();

            return $this->redirectToRoute('social_event_show', ['department' => $department->getId(), 'semester' => $semester->getId()]);
        }

        return $this->render('social_event/social_event_create.html.twig', [
            'form' => $form->createView(),
            'department' => $department,
            'semester' => $semester,
            'event' => $socialEvent,
        ]);
    }

    #[Route('/kontrollpanel/arrangement/endre/{id}', name: 'social_event_edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function editSocialEventAction(SocialEvent $social_event, Request $request)
    {
        $form = $this->createForm(SocialEventType::class, $social_event, [
            'department' => $social_event->getDepartment(),
            'semester' => $social_event->getSemester(),
        ]);
        $form->handleRequest($request);

        $department = $this->getDepartmentOrThrow404($request);
        $semester = $this->getSemesterOrThrow404($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($social_event);
            $this->em->flush();

            return $this->redirectToRoute('social_event_show', ['department' => $department->getId(), 'semester' => $semester->getId()]);
        }

        return $this->render('social_event/social_event_create.html.twig', [
            'form' => $form->createView(),
            'department' => $department,
            'semester' => $semester,
            'event' => $social_event,
        ]);
    }

    /**
     * @return RedirectResponse
     */
    #[Route('/kontrollpanel/arrangement/slett/{id}', name: 'social_event_delete', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function deleteSocialEventAction(Request $request, SocialEvent $event)
    {
        // NOTE: this function will permanently remove the event.
        $semester = $this->getSemesterOrThrow404($request);
        $department = $this->getDepartmentOrThrow404($request);

        $this->em->remove($event);
        $this->em->flush();

        return $this->redirectToRoute('social_event_show', ['department' => $department->getId(), 'semester' => $semester->getId()]);
    }
}
