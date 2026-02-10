<?php

namespace App\Controller;

use App\Entity\Department;
use App\Entity\ExecutiveBoard;
use App\Entity\Repository\DepartmentRepository;
use App\Entity\Repository\ExecutiveBoardRepository;
use App\Entity\Repository\SemesterRepository;
use App\Entity\SupportTicket;
use App\Event\SupportTicketCreatedEvent;
use App\Form\Type\SupportTicketType;
use App\Service\GeoLocation;
use App\Service\LogService;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends BaseController
{
    public function __construct(
        private GeoLocation $geoLocation,
        private DepartmentRepository $departmentRepo,
        private ExecutiveBoardRepository $executiveBoardRepo,
        private LogService $logService,
        private EventDispatcherInterface $eventDispatcher,
        SemesterRepository $semesterRepo,
    ) {
        parent::__construct($departmentRepo, $semesterRepo);
    }

    /**
     * @Route("/kontakt/avdeling/{id}",
     *     name="contact_department",
     *     methods={"GET", "POST"})
     *
     * @Route("/kontakt",
     *     name="contact",
     *     methods={"GET", "POST"})
     *
     * @param Request $request
     * @param Department|null $department
     *
     * @return Response
     */
    public function indexAction(Request $request, Department $department = null)
    {
        if ($department === null) {
            $department = $this->geoLocation
                ->findNearestDepartment($this->departmentRepo->findAll());
        }

        $supportTicket = new SupportTicket();
        $supportTicket->setDepartment($department);
        $form = $this->createForm(SupportTicketType::class, $supportTicket, array(
            'department_repository' => $this->departmentRepo,
        ));

        $form->handleRequest($request);
        if ($form->isSubmitted() && $supportTicket->getDepartment() === null) {
            $this->logService->error("Could not send support ticket. Department was null.\n$supportTicket");
        }
        if ($form->isSubmitted() && $form->isValid()) {
            $this->eventDispatcher
            ->dispatch(new SupportTicketCreatedEvent($supportTicket), SupportTicketCreatedEvent::NAME);

            return $this->redirectToRoute('contact_department', array('id' => $supportTicket->getDepartment()->getId()));
        }

        $board = $this->executiveBoardRepo->findBoard();
        $scrollToForm = $form->isSubmitted() && !$form->isValid();

        return $this->render('contact/index.html.twig', array(
            'form' => $form->createView(),
            'specific_department' => $department,
            'board' => $board,
            'scrollToForm' => $scrollToForm
        ));
    }
}
