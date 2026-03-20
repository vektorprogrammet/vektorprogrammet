<?php

namespace App\Admission\Controller;

use App\Support\Controller\BaseController;
use App\Admission\Infrastructure\Entity\AdmissionSubscriber;
use App\Organization\Infrastructure\Entity\Department;
use App\Admission\Infrastructure\Repository\AdmissionSubscriberRepository;
use App\Organization\Infrastructure\Repository\DepartmentRepository;
use App\Shared\Repository\SemesterRepository;
use App\Admission\Form\AdmissionSubscriberType;
use App\Admission\Infrastructure\AdmissionNotifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AdmissionSubscriberController extends BaseController
{
    public function __construct(
        private readonly DepartmentRepository $departmentRepo,
        private readonly AdmissionSubscriberRepository $admissionSubscriberRepo,
        private readonly EntityManagerInterface $em,
        private readonly AdmissionNotifier $admissionNotifier,
        SemesterRepository $semesterRepo,
    ) {
        parent::__construct($departmentRepo, $semesterRepo);
    }

    /**
     * @return Response
     */
    #[Route('/interesseliste/{shortName}', name: 'interest_list', requirements: ['shortName' => "\w+"])]
    #[Route('/interesseliste/{id}', name: 'interest_list_by_id', requirements: ['id' => "\d+"])]
    public function subscribePageAction(Request $request, Department $department)
    {
        $subscriber = new AdmissionSubscriber();
        $subscriber->setDepartment($department);

        $form = $this->createForm(AdmissionSubscriberType::class, $subscriber);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->admissionNotifier->createSubscription($department, $subscriber->getEmail(), $subscriber->getInfoMeeting());
                $this->addFlash('success', $subscriber->getEmail().' har blitt meldt på interesselisten. Du vil få en e-post når opptaket starter');
            } catch (\InvalidArgumentException) {
                $this->addFlash('danger', 'Kunne ikke melde '.$subscriber->getEmail().' på interesselisten. Vennligst prøv igjen.');
            }

            return $this->redirectToRoute('interest_list', ['shortName' => $department->getShortName()]);
        }

        return $this->render('admission_subscriber/subscribe_page.html.twig', [
            'department' => $department,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @return Response
     */
    #[Route('/opptak/notification', name: 'admission_subscribe')]
    public function subscribeAction(Request $request)
    {
        $email = $request->request->get('email');
        $departmentId = $request->request->get('department');
        $infoMeeting = filter_var($request->request->get('infoMeeting'), FILTER_VALIDATE_BOOLEAN);
        if (!$email || !$departmentId) {
            return new JsonResponse('Email or department missing', 400);
        }
        $department = $this->departmentRepo->find($departmentId);
        if (!$department) {
            return new JsonResponse('Invalid department', 400);
        }

        try {
            $this->admissionNotifier->createSubscription($department, $email, $infoMeeting);
        } catch (\InvalidArgumentException $e) {
            return new JsonResponse($e->getMessage(), 400);
        }

        return new JsonResponse(null, 201);
    }

    /**
     * @param string $code
     *
     * @return RedirectResponse
     */
    #[Route('/opptak/notification/unsubscribe/{code}', name: 'admission_unsubscribe')]
    public function unsubscribeAction($code)
    {
        $subscriber = $this->admissionSubscriberRepo->findByUnsubscribeCode($code);
        $this->addFlash('title', 'Opptaksvarsel - Avmelding');
        if ($subscriber === null) {
            $this->addFlash('message', 'Du vil ikke lengre motta varsler om opptak');
        } else {
            $email = $subscriber->getEmail();
            $this->addFlash('message', "Du vil ikke lengre motta varsler om opptak på $email");
            $this->em->remove($subscriber);
            $this->em->flush();
        }

        return $this->redirectToRoute('confirmation');
    }
}
