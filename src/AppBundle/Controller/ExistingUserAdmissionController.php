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
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function showAction(Request $request)
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $admissionManager = $this->get('app.application_admission');
        if ($res = $admissionManager->renderErrorPage($user)) {
            return $res;
        }

        $department = $user->getDepartment();
        $teams = $em->getRepository('AppBundle:Team')->findByDepartment($department);

        $application = $admissionManager->createApplicationForExistingAssistant($user);

        $form = $this->createForm(new ApplicationExistingUserType(), $application, array(
            'validation_groups' => array('admission_existing'),
            'teams' => $teams,
        ));
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->persist($application);
            $em->flush();

            $this->get('event_dispatcher')->dispatch(ApplicationCreatedEvent::NAME, new ApplicationCreatedEvent($application));
            $this->addFlash("success", "Søknad mottatt!");

            return $this->redirectToRoute('my_page');
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
