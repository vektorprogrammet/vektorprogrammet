<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Department;
use AppBundle\Entity\ExecutiveBoard;
use AppBundle\Entity\SupportTicket;
use AppBundle\Event\SupportTicketCreatedEvent;
use AppBundle\Form\Type\SupportTicketType;
use AppBundle\Repository\Contract\DepartmentRepositoryInterface;
use AppBundle\Service\Contract\GeoLocationInterface;
use AppBundle\Service\Contract\LogServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends BaseController
{
    private $geoLocation;
    private $departmentRepository;
    private $logService;
    private $eventDispatcher;
    private $entityManager;

    /**
     * @param GeoLocationInterface $geoLocation
     * @param DepartmentRepositoryInterface $departmentRepository
     * @param LogServiceInterface $logService
     * @param EventDispatcherInterface $eventDispatcher
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        GeoLocationInterface $geoLocation,
        DepartmentRepositoryInterface $departmentRepository,
        LogServiceInterface $logService,
        EventDispatcherInterface $eventDispatcher,
        EntityManagerInterface $entityManager
    ) {
        $this->geoLocation = $geoLocation;
        $this->departmentRepository = $departmentRepository;
        $this->logService = $logService;
        $this->eventDispatcher = $eventDispatcher;
        $this->entityManager = $entityManager;
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
                ->findNearestDepartment($this->departmentRepository->findAllDepartments());
        }

        $supportTicket = new SupportTicket();
        $supportTicket->setDepartment($department);
        $form = $this->createForm(SupportTicketType::class, $supportTicket, array(
            'department_repository' => $this->departmentRepository,
        ));

        $form->handleRequest($request);
        if ($form->isSubmitted() && $supportTicket->getDepartment() === null) {
            $this->logService->error("Could not send support ticket. Department was null.\n$supportTicket");
        }
        if ($form->isSubmitted() && $form->isValid()) {
            $this->eventDispatcher
            ->dispatch(SupportTicketCreatedEvent::NAME, new SupportTicketCreatedEvent($supportTicket));

            return $this->redirectToRoute('contact_department', array('id' => $supportTicket->getDepartment()->getId()));
        }

        $board = $this->entityManager->getRepository(ExecutiveBoard::class)->findBoard();
        $scrollToForm = $form->isSubmitted() && !$form->isValid();

        return $this->render('contact/index.html.twig', array(
            'form' => $form->createView(),
            'specific_department' => $department,
            'board' => $board,
            'scrollToForm' => $scrollToForm
        ));
    }
}
