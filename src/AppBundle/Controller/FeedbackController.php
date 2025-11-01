<?php
namespace AppBundle\Controller;

use AppBundle\Entity\Feedback;
use AppBundle\Form\Type\FeedbackType;
use AppBundle\Repository\Contract\FeedbackRepositoryInterface;
use AppBundle\Service\Contract\FeedbackSubmissionServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;

class FeedbackController extends BaseController
{
    private $entityManager;
    private $feedbackSubmissionService;
    private $feedbackRepository;
    private $paginator;

    /**
     * @param EntityManagerInterface $entityManager
     * @param FeedbackSubmissionServiceInterface $feedbackSubmissionService
     * @param FeedbackRepositoryInterface $feedbackRepository
     * @param PaginatorInterface $paginator
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        FeedbackSubmissionServiceInterface $feedbackSubmissionService,
        FeedbackRepositoryInterface $feedbackRepository,
        PaginatorInterface $paginator
    ) {
        $this->entityManager = $entityManager;
        $this->feedbackSubmissionService = $feedbackSubmissionService;
        $this->feedbackRepository = $feedbackRepository;
        $this->paginator = $paginator;
    }
    //shows form for submitting a new feedback
    public function indexAction(Request $request)
    {
        $feedback = new Feedback;
        $user = $this->getUser();

        $form = $this->createForm(FeedBackType::class, $feedback);
        $form->handleRequest($request);

        $returnUri = $request->getUri();
        if ($request->headers->get('referer')) {
            $returnUri = $request->headers->get('referer');
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $feedback = $form->getData();
            $this->feedbackSubmissionService->submitFeedback($feedback, $user);

            $this->addFlash("success", "Tilbakemeldingen har blitt registrert, tusen takk!");
            
            return $this->redirect($returnUri);
        }

        return $this->render('feedback_admin/feedback_admin_index.html.twig', array(
            'title' => 'Feedback'
        ));
    }
    //Shows a specific feedback
    public function showAction(Request $request, Feedback $feedback)
    {
        return $this->render('feedback_admin/feedback_admin_show.html.twig', array(
            'feedback' => $feedback,
            'title' => $feedback->getTitle(),
        ));
    }

    //Lists all feedbacks
    public function showAllAction(Request $request)
    {
        //Gets all feedbacks sorted by created_at
        $feedbacks = $this->feedbackRepository->findAllSortByNewest();

        $pagination = $this->paginator->paginate(
            $feedbacks,
            $request->query->get('page', 1),
            15
        );

        return $this->render('feedback_admin/feedback_admin_list.html.twig', array(
            'feedbacks' => $feedbacks,
            'pagination' => $pagination,
            'title' => 'Alle tilbakemeldinger'
        ));
    }
    public function deleteAction(Feedback $feedback)
    {
        $this->entityManager->remove($feedback);
        $this->entityManager->flush();

        $this->addFlash("success", "\"". $feedback->getTitle()."\" ble slettet");

        return $this->redirect($this->generateUrl('feedback_admin_list'));
    }
}
