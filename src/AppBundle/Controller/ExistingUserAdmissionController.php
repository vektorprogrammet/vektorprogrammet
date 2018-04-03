<?php

namespace AppBundle\Controller;

use AppBundle\Event\ApplicationCreatedEvent;
use AppBundle\Form\Type\ApplicationExistingUserType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ExistingUserAdmissionController extends Controller
{
    /**
     * @Route("/eksisterendeopptak", name="admission_existing_user")
     * @Method({"GET", "POST"})
     *
     * @param Request $request
     *
     * @return null|\Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Request $request)
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $admissionManager = $this->get('app.application_admission');

        if ($res = $admissionManager->renderErrorPage($user)) {
            return $res;
        }

        $application = $admissionManager->createApplicationForExistingAssistant($user);

        $form = $this->createForm(new ApplicationExistingUserType(), $application, array(
            'validation_groups' => array('admission_existing'),
        ));
        $form->remove('applicationPractical.teamInterest');
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->persist($application);
            $em->flush();

            $this->get('event_dispatcher')->dispatch(ApplicationCreatedEvent::NAME, new ApplicationCreatedEvent($application));

            return $this->redirectToRoute('application_confirmation');
        }

        $semester = $em->getRepository('AppBundle:Semester')->findSemesterWithActiveAdmissionByDepartment($user->getDepartment());

        return $this->render(':admission:existingUser.html.twig', array(
            'form' => $form->createView(),
            'department' => $user->getDepartment(),
            'semester' => $semester,
            'user' => $user,
        ));
    }
}
