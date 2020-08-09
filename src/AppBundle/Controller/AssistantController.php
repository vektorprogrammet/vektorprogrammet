<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Application;
use AppBundle\Entity\Department;
use AppBundle\Event\ApplicationCreatedEvent;
use AppBundle\Form\Type\ApplicationType;
use AppBundle\Service\ApplicationAdmission;
use AppBundle\Service\FilterService;
use AppBundle\Service\GeoLocation;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AssistantController extends BaseController
{
    /**
     * @deprecated This resource is only here to serve old urls (e.g. in old emails)
     *
     * @Route("/opptak/{shortName}",
     *     requirements={"shortName"="(NTNU|NMBU|UiB|UIB|UiO|UIO)"})
     * @Route("/avdeling/{shortName}",
     *     requirements={"shortName"="(NTNU|NMBU|UiB|UIB|UiO|UIO)"})
     * @Route("/opptak/avdeling/{id}",
     *     requirements={"id"="\d+"})
     * @Method({"GET", "POST"})
     *
     * @param Request $request
     * @param Department $department
     *
     * @return Response
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function admissionByShortNameAction(Request $request, Department $department)
    {
        return $this->indexAction($request, $department);
    }
    
    /**
     * @Route("/opptak/{city}", name="admission_show_by_city_case_insensitive")
     * @Route("/avdeling/{city}", name="admission_show_specific_department_by_city_case_insensitive")
     *
     * @param Request $request
     * @param $city
     *
     * @return Response
     */
    public function admissionCaseInsensitiveAction(Request $request, $city)
    {
        $city = str_replace(array('æ', 'ø','å'), array('Æ','Ø','Å'), $city); // Make sqlite happy
        $department = $this->getDoctrine()
                ->getRepository('AppBundle:Department')
                ->findOneByCityCaseInsensitive($city);
        if ($department !== null) {
            return $this->indexAction($request, $department);
        } else {
            throw $this->createNotFoundException("Fant ingen avdeling $city.");
        }
    }

    /**
     * @Route("/opptak")
     * @Method({"GET", "POST"})
     *
     * @param Request $request
     * @param Department $department
     *
     * @return Response
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function admissionAction(Request $request, Department $department = null)
    {
        return $this->indexAction($request, $department);
    }

    /**
     * @param Request $request
     * @param Department $specificDepartment
     * @param bool $scrollToAdmissionForm
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function indexAction(Request $request, Department $specificDepartment = null, $scrollToAdmissionForm = false)
    {
        $admissionManager = $this->get(ApplicationAdmission::class);
        $em = $this->getDoctrine()->getManager();

        $departments = $em->getRepository('AppBundle:Department')->findActive();
        $departments = $this->get(GeoLocation::class)->sortDepartmentsByDistanceFromClient($departments);
        $departmentsWithActiveAdmission = $this->get(FilterService::class)->filterDepartmentsByActiveAdmission($departments, true);

        $departmentInUrl = $specificDepartment !== null;
        if (!$departmentInUrl) {
            $specificDepartment = $departments[0];
        }

        $teams = $em->getRepository('AppBundle:Team')->findByOpenApplicationAndDepartment($specificDepartment);

        $application = new Application();

        $formViews = array();

        /** @var Department $department */
        foreach ($departments as $department) {
            $form = $this->get('form.factory')->createNamedBuilder('application_'.$department->getId(), ApplicationType::class, $application, array(
                'validation_groups' => array('admission'),
                'departmentId' => $department->getId(),
                'environment' => $this->get('kernel')->getEnvironment(),
            ))->getForm();

            $form->handleRequest($request);

            if ($form->isSubmitted()) {
                $scrollToAdmissionForm = true;
                $specificDepartment = $department;
            }

            if ($form->isSubmitted() && $form->isValid()) {
                $admissionManager->setCorrectUser($application);

                if ($application->getUser()->hasBeenAssistant()) {
                    return $this->redirectToRoute('admission_existing_user');
                }

                $admissionPeriod = $em->getRepository('AppBundle:AdmissionPeriod')->findOneWithActiveAdmissionByDepartment($department);

                //If no active admission period is found
                if (!$admissionPeriod) {
                    $this->addFlash('danger', $department . ' sitt opptak er dessverre stengt.');
                    return $this->redirectToRoute('assistants');
                }
                $application->setAdmissionPeriod($admissionPeriod);
                $em->persist($application);
                $em->flush();

                $this->get('event_dispatcher')->dispatch(ApplicationCreatedEvent::NAME, new ApplicationCreatedEvent($application));

                return $this->redirectToRoute('application_confirmation');
            }

            $formViews[$department->getCity()] = $form->createView();
        }

        return $this->render('assistant/assistants.html.twig', array(
            'specific_department' => $specificDepartment,
            'department_in_url' => $departmentInUrl,
            'departments' => $departments,
            'departmentsWithActiveAdmission' => $departmentsWithActiveAdmission,
            'teams' => $teams,
            'forms' => $formViews,
            'scroll_to_admission_form' => $scrollToAdmissionForm,
        ));
    }

    /**
     * @Route("/assistenter/opptak/bekreftelse", name="application_confirmation")
     * @return Response
     */
    public function confirmationAction()
    {
        return $this->render('admission/application_confirmation.html.twig');
    }

    /**
     * @Route("/stand/opptak/{shortName}", name="application_stand_form", requirements={"shortName"="\w+"})
     *
     * @param Request $request
     * @param Department $department
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function subscribePageAction(Request $request, Department $department)
    {
        if (!$department->activeAdmission()) {
            return $this->indexAction($request, $department);
        }
        $admissionManager = $this->get(ApplicationAdmission::class);
        $em = $this->getDoctrine()->getManager();
        $application = new Application();

        $form = $this->get('form.factory')->createNamedBuilder('application_'.$department->getId(), ApplicationType::class, $application, array(
            'validation_groups' => array('admission'),
            'departmentId' => $department->getId(),
            'environment' => $this->get('kernel')->getEnvironment(),
        ))->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $admissionManager->setCorrectUser($application);

            if ($application->getUser()->hasBeenAssistant()) {
                $this->addFlash('warning', $application->getUser()->getEmail().' har vært assistent før. Logg inn med brukeren din for å søke igjen.');
                return $this->redirectToRoute('application_stand_form', ['shortName' => $department->getShortName()]);
            }

            $admissionPeriod = $em->getRepository('AppBundle:AdmissionPeriod')->findOneWithActiveAdmissionByDepartment($department);
            $application->setAdmissionPeriod($admissionPeriod);
            $em->persist($application);
            $em->flush();

            $this->get('event_dispatcher')->dispatch(ApplicationCreatedEvent::NAME, new ApplicationCreatedEvent($application));

            $this->addFlash('success', $application->getUser()->getEmail().' har blitt registrert. Du vil få en e-post med kvittering på søknaden.');
            return $this->redirectToRoute('application_stand_form', ['shortName' => $department->getShortName()]);
        }

        return $this->render('admission/application_page.html.twig', [
            'department' => $department,
            'form' => $form->createView()
        ]);
    }
}
