<?php

namespace App\Controller;

use App\Entity\Department;
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
use Symfony\Component\Routing\Attribute\Route;

class ContactController extends BaseController
{
    public function __construct(
        private readonly GeoLocation $geoLocation,
        private readonly DepartmentRepository $departmentRepo,
        private readonly ExecutiveBoardRepository $executiveBoardRepo,
        private readonly LogService $logService,
        private readonly EventDispatcherInterface $eventDispatcher,
        SemesterRepository $semesterRepo,
    ) {
        parent::__construct($departmentRepo, $semesterRepo);
    }

    /**
     * @return Response
     */
    #[Route('/kontakt/avdeling/{id}', name: 'contact_department', methods: ['GET', 'POST'])]
    #[Route('/kontakt', name: 'contact', methods: ['GET', 'POST'])]
    public function indexAction(Request $request, ?Department $department = null)
    {
        if ($department === null) {
            $department = $this->geoLocation
                ->findNearestDepartment($this->departmentRepo->findAll());
        }

        $supportTicket = new SupportTicket();
        $supportTicket->setDepartment($department);
        $form = $this->createForm(SupportTicketType::class, $supportTicket, [
            'department_repository' => $this->departmentRepo,
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $supportTicket->getDepartment() === null) {
            $this->logService->error("Could not send support ticket. Department was null.\n$supportTicket");
        }
        if ($form->isSubmitted() && $form->isValid()) {
            $this->eventDispatcher
            ->dispatch(new SupportTicketCreatedEvent($supportTicket), SupportTicketCreatedEvent::NAME);

            return $this->redirectToRoute('contact_department', ['id' => $supportTicket->getDepartment()->getId()]);
        }

        $board = $this->executiveBoardRepo->findBoard();
        $scrollToForm = $form->isSubmitted() && !$form->isValid();

        return $this->render('contact/index.html.twig', [
            'form' => $form->createView(),
            'specific_department' => $department,
            'board' => $board,
            'scrollToForm' => $scrollToForm,
        ]);
    }
}
