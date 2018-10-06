<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Application;
use AppBundle\Entity\Department;
use AppBundle\Event\ApplicationCreatedEvent;
use AppBundle\Form\Type\ApplicationType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AssistantController extends BaseController
{
    /**
     * @deprecated This route is only here to serve old urls (e.g. in old emails)
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
    public function admissionActionByShortName(Request $request, Department $department)
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
    public function admissionActionCaseInsensitive(Request $request, $city)
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
        $admissionManager = $this->get('app.application_admission');
        $em = $this->getDoctrine()->getManager();

        $departments = $em->getRepository('AppBundle:Department')->findActive();
        $departments = $this->get('app.geolocation')->sortDepartmentsByDistanceFromClient($departments);
        $departmentsWithActiveAdmission = $this->get('app.filter_service')->filterDepartmentsByActiveAdmission($departments, true);

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

                $semester = $em->getRepository('AppBundle:Semester')->findSemesterWithActiveAdmissionByDepartment($department);
                $application->setSemester($semester);
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
}
