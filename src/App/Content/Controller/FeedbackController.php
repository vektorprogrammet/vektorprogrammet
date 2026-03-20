<?php

namespace App\Content\Controller;

use App\Support\Controller\BaseController;
use App\Content\Infrastructure\Entity\Feedback;
use App\Entity\Repository\DepartmentRepository;
use App\Content\Infrastructure\Repository\FeedbackRepository;
use App\Shared\Repository\SemesterRepository;
use App\Content\Form\FeedbackType;
use App\Support\Infrastructure\Slack\SlackMessenger;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class FeedbackController extends BaseController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly SlackMessenger $slackMessenger,
        private readonly PaginatorInterface $paginator,
        private readonly FeedbackRepository $feedbackRepo,
        DepartmentRepository $departmentRepo,
        SemesterRepository $semesterRepo,
    ) {
        parent::__construct($departmentRepo, $semesterRepo);
    }

    // shows form for submitting a new feedback
    #[Route('/kontrollpanel/feedback', name: 'feedback_admin_index', methods: ['GET', 'POST'])]
    public function indexAction(Request $request)
    {
        $feedback = new Feedback();
        $user = $this->getUser();

        $form = $this->createForm(FeedbackType::class, $feedback);
        $form->handleRequest($request);

        $returnUri = $request->getUri();
        if ($request->headers->get('referer')) {
            $returnUri = $request->headers->get('referer');
        }

        if ($form->isSubmitted() && $form->isValid()) {
            // Stores the submitted feedback
            $feedback = $form->getData();
            $feedback->setUser($user);
            $this->em->persist($feedback);
            $this->em->flush();

            // Notifies on slack (NotificationChannel)
            $this->slackMessenger->notify($feedback->getSlackMessageBody());

            $this->addFlash('success', 'Tilbakemeldingen har blitt registrert, tusen takk!');

            return $this->redirect($returnUri); // Makes sure the user cannot submit the same form twice (e.g. by reloading page)// Will also r
        }

        return $this->render('feedback_admin/feedback_admin_index.html.twig', [
            'title' => 'Feedback',
        ]);
    }

    // Shows a specific feedback
    #[Route('/kontrollpanel/feedback/show/{id}', name: 'feedback_admin_show', methods: ['GET'])]
    public function showAction(Request $request, Feedback $feedback)
    {
        return $this->render('feedback_admin/feedback_admin_show.html.twig', [
            'feedback' => $feedback,
            'title' => $feedback->getTitle(),
        ]);
    }

    // Lists all feedbacks
    #[Route('/kontrollpanel/feedback/list', name: 'feedback_admin_list', methods: ['GET'])]
    public function showAllAction(Request $request)
    {
        // Gets all feedbacks sorted by created_at
        $feedbacks = $this->feedbackRepo->findAllSortByNewest();

        $pagination = $this->paginator->paginate(
            $feedbacks,
            $request->query->get('page', 1),
            15
        );

        return $this->render('feedback_admin/feedback_admin_list.html.twig', [
            'feedbacks' => $feedbacks,
            'pagination' => $pagination,
            'title' => 'Alle tilbakemeldinger',
        ]);
    }

    #[Route('/kontrollpanel/feedback/delete/{id}', name: 'feedback_admin_delete', methods: ['POST'])]
    public function deleteAction(Feedback $feedback)
    {
        $this->em->remove($feedback);
        $this->em->flush();

        $this->addFlash('success', '"'.$feedback->getTitle().'" ble slettet');

        return $this->redirect($this->generateUrl('feedback_admin_list'));
    }
}
