<?php
namespace AppBundle\Controller;

use AppBundle\Entity\Feedback;
use AppBundle\Form\Type\FeedbackType;
use Doctrine\ORM\EntityManagerInterface;
use AppBundle\Service\Contract\SlackMessengerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class FeedbackController extends BaseController
{
    private $entityManager;
    private $slackMessenger;
    private $paginator;

    /**
     * @param EntityManagerInterface $entityManager
     * @param SlackMessengerInterface $slackMessenger
     * @param PaginatorInterface $paginator
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        SlackMessengerInterface $slackMessenger,
        PaginatorInterface $paginator
    ) {
        $this->entityManager = $entityManager;
        $this->slackMessenger = $slackMessenger;
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
            //Stores the submitted feedback
            $feedback = $form->getData();
            $feedback->setUser($user);
            $this->entityManager->persist($feedback);
            $this->entityManager->flush();

            //Notifies on slack (NotificationChannel)
            $this->slackMessenger->notify($feedback->getSlackMessageBody());

            $this->addFlash("success", "Tilbakemeldingen har blitt registrert, tusen takk!");
            
            return $this->redirect($returnUri); //Makes sure the user cannot submit the same form twice (e.g. by reloading page)// Will also r
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
        // TODO: Create FeedbackRepositoryInterface and inject it
        $repository = $this->entityManager->getRepository(Feedback::class);

        //Gets all feedbacks sorted by created_at
        $feedbacks = $repository->findAllSortByNewest();

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
