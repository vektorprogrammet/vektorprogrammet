<?php

namespace App\Controller;

use App\Entity\Repository\DepartmentRepository;
use App\Entity\Repository\SemesterRepository;
use App\Entity\Repository\SocialEventRepository;
use App\Entity\SocialEvent;
use App\Form\Type\SocialEventType;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SocialEventController extends BaseController
{
    public function __construct(
        private EntityManagerInterface $em,
        private SocialEventRepository $socialEventRepo,
        DepartmentRepository $departmentRepo,
        SemesterRepository $semesterRepo,
    ) {
        parent::__construct($departmentRepo, $semesterRepo);
    }

    /**
     * @param Request $request
     * @return Response|null
     */
    public function showAction(Request $request)
    {
        $department = $this->getDepartmentOrThrow404($request);
        $semester = $this->getSemesterOrThrow404($request);

        $SocialEventList = $this->socialEventRepo->findSocialEventsBySemesterAndDepartment($semester, $department);


        return $this->render("social_event/social_event_list.twig", array(
            'department' => $department,
            'semester' => $semester,
            'SocialEventList' => $SocialEventList,
            'now' => new DateTime(),
        ));
    }

    /**
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function createSocialEventAction(Request $request)
    {
        $department = $this->getDepartmentOrThrow404($request);
        $semester = $this->getSemesterOrThrow404($request);
        $socialEvent    = new SocialEvent();
        $user            = $this->getUser();

        $form = $this->createForm(SocialEventType::class, $socialEvent, array(
            'department'        => $department,
            'semester'          => $semester,
        ));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($socialEvent);
            $this->em->flush();
            return $this->redirectToRoute('social_event_show', ['department'=> $department->getId(), 'semester'=>$semester->getId()]);
        }

        return $this->render('social_event/social_event_create.html.twig', array(
            'form' => $form->createView(),
            'department' => $department,
            'semester' => $semester,
            'event' => $socialEvent
        ));
    }

    public function editSocialEventAction(SocialEvent $social_event, Request $request)
    {
        $form = $this->createForm(SocialEventType::class, $social_event, array(
            'department'        => $social_event->getDepartment(),
            'semester'          => $social_event->getSemester(),
        ));
        $form->handleRequest($request);

        $department = $this->getDepartmentOrThrow404($request);
        $semester = $this->getSemesterOrThrow404($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($social_event);
            $this->em->flush();
            return $this->redirectToRoute('social_event_show', ['department'=> $department->getId(), 'semester'=>$semester->getId()]);
        }

        return $this->render('social_event/social_event_create.html.twig', array(
            'form' => $form->createView(),
            'department' => $department,
            'semester' => $semester,
            'event' => $social_event,
        ));
    }

    /**
     * @param Request $request
     * @param SocialEvent $event
     * @return RedirectResponse
     */
    public function deleteSocialEventAction(Request $request, SocialEvent $event)
    {
        # NOTE: this function will permanently remove the event.
        $semester = $this->getSemesterOrThrow404($request);
        $department = $this->getDepartmentOrThrow404($request);

        $this->em->remove($event);
        $this->em->flush();

        return $this->redirectToRoute('social_event_show', ['department'=> $department->getId(), 'semester'=>$semester->getId()]);
    }
}
