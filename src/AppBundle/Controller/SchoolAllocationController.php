<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Application;
use AppBundle\Entity\Semester;
use AppBundle\SchoolAllocation\Assistant;
use AppBundle\SchoolAllocation\Node;
use AppBundle\SchoolAllocation\Optimizer;
use AppBundle\SchoolAllocation\School;
use AppBundle\SchoolAllocation\Allocation;
use Doctrine\ORM\NoResultException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\SchoolCapacity;
use AppBundle\Form\Type\SchoolCapacityType;
use Symfony\Component\HttpFoundation\Response;

class SchoolAllocationController extends Controller
{

    public function showAction(Request $request, $departmentId = null)
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();

        if (null === $departmentId) {
            $departmentId = $user->getFieldOfStudy()->getDepartment()->getId();
        }

        $currentSemester = $this->getDoctrine()->getRepository('AppBundle:Semester')->findCurrentSemesterByDepartment($departmentId);

        $applications = $this->getDoctrine()->getRepository('AppBundle:Application')->findAllAllocatableApplicationsBySemester($currentSemester);
        $allCurrentSchoolCapacities = $this->getDoctrine()->getRepository('AppBundle:SchoolCapacity')->findBySemester($currentSemester);

        $schools = $this->generateSchoolsFromSchoolCapacities($allCurrentSchoolCapacities);


        return $this->render('school_admin/school_allocate_show.html.twig', array(
            'semester' => $currentSemester,
            'applications' => $applications,
            'allocations' => $allCurrentSchoolCapacities,
            'allocatedSchools' => $schools,
        ));
    }


    private function generateSchoolsFromSchoolCapacities($schoolCapacities)
    {
        //Use schoolCapacities to create School objects for the SA-Algorithm
        $schools = array();
        foreach ($schoolCapacities as $sc) {
            if ($sc->getMonday() == 0 && $sc->getTuesday() == 0 && $sc->getWednesday() == 0 && $sc->getThursday() == 0 && $sc->getFriday() == 0) continue;
            $capacityDays = array();
            $capacityDays["Monday"] = $sc->getMonday();
            $capacityDays["Tuesday"] = $sc->getTuesday();
            $capacityDays["Wednesday"] = $sc->getWednesday();
            $capacityDays["Thursday"] = $sc->getThursday();
            $capacityDays["Friday"] = $sc->getFriday();

            $capacity = array();
            $capacity[1] = $capacityDays;
            $capacity[2] = $capacityDays;

            $school = new School($capacity, $sc->getSchool()->getName());
            $schools[] = $school;
        }
        return $schools;
    }

    /**
     * @param Application[] $applications
     * @return Assistant[]
     */
    private function generateAssistantsFromApplications($applications)
    {
        //Use applications to create Assistant objects for the allocation algorithm
        $assistants = array();
        foreach ($applications as $application) {
            $doublePosition = $application->getDoublePosition();
            $preferredGroup = null;
            if($application->getPreferredGroup() == "Bolk 1")$preferredGroup = 1;
            elseif($application->getPreferredGroup() == "Bolk 2")$preferredGroup = 2;
            if($doublePosition)$preferredGroup = null;

            $availability = array();
            $availabilityPoints = ["Ikke", "Bra"];
            $availability["Monday"] = array_search($application->getMonday(), $availabilityPoints);
            $availability["Tuesday"] = array_search($application->getTuesday(), $availabilityPoints);
            $availability["Wednesday"] = array_search($application->getWednesday(), $availabilityPoints);
            $availability["Thursday"] = array_search($application->getThursday(), $availabilityPoints);
            $availability["Friday"] = array_search($application->getFriday(), $availabilityPoints);

            $assistant = new Assistant();
            $assistant->setName($application->getUser()->getFirstName() . ' ' . $application->getUser()->getLastName());
            $assistant->setDoublePosition($doublePosition);
            $assistant->setPreferredGroup($preferredGroup);
            $assistant->setAvailability($availability);
            $assistants[] = $assistant;
        }
        return $assistants;
    }

    public function getApplicationsAction(){
        $user = $this->get('security.token_storage')->getToken()->getUser();

        $departmentId = $user->getFieldOfStudy()->getDepartment()->getId();

        $currentSemester = $this->getDoctrine()->getRepository('AppBundle:Semester')->findCurrentSemesterByDepartment($departmentId);

        $applications = $this->getDoctrine()->getRepository('AppBundle:Application')->findAllAllocatableApplicationsBySemester($currentSemester);


    }

    public function allocateAction(Request $request)
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();

        $departmentId = $user->getFieldOfStudy()->getDepartment()->getId();

        $currentSemester = $this->getDoctrine()->getRepository('AppBundle:Semester')->findCurrentSemesterByDepartment($departmentId);

        $allCurrentSchoolCapacities = $this->getDoctrine()->getRepository('AppBundle:SchoolCapacity')->findBySemester($currentSemester);
        $applications = $this->getDoctrine()->getRepository('AppBundle:Application')->findAllAllocatableApplicationsBySemester($currentSemester);
        $assistants = $this->generateAssistantsFromApplications($applications);
        $schools = $this->generateSchoolsFromSchoolCapacities($allCurrentSchoolCapacities);
        $allocation = new Allocation($schools, $assistants);
        $result = $allocation->step();
        dump($result);


        //Total number of allocations evaluated during optimization

        return $this->render('school_admin/school_allocate_result.html.twig', array(
            'assistants' => $result->getAssistants(),
        ));
    }

    public function createAction(Request $request, $departmentId = null)
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();

        if (null === $departmentId) {
            $departmentId = $user->getFieldOfStudy()->getDepartment()->getId();
        }
        $allSemesters = $this->getDoctrine()->getRepository('AppBundle:Semester')->findByDepartment($departmentId);
        $currentSemester = null;
        foreach ($allSemesters as $semester) {
            $now = new \DateTime();
            if ($semester->getSemesterStartDate() < $now && $semester->getSemesterEndDate() > $now) {
                $currentSemester = $semester;
                break;
            }
        }

        $schoolCapacity = new SchoolCapacity();
        $schoolCapacity->setSemester($currentSemester);
        $form = $this->createForm(new SchoolCapacityType(), $schoolCapacity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            try {
                $exists = $this->getDoctrine()->getRepository('AppBundle:SchoolCapacity')->findBySchoolAndSemester($schoolCapacity->getSchool(), $schoolCapacity->getSemester());
                return $this->render('school_admin/school_allocate_create.html.twig', array(
                    'message' => 'Skolen eksisterer allerede',
                    'form' => $form->createView(),
                ));
            } catch (NoResultException $e) {

            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($schoolCapacity);
            $em->flush();
            return $this->redirect($this->generateUrl('school_allocation'));
        }

        return $this->render('school_admin/school_allocate_create.html.twig', array(
            'message' => '',
            'form' => $form->createView(),
        ));
    }

    public function editAction(Request $request)
    {
        $schoolId = $request->query->get("school");
        $semesterId = $request->query->get("semester");
        $school = $this->getDoctrine()->getRepository('AppBundle:School')->find($schoolId);
        $semester = $this->getDoctrine()->getRepository('AppBundle:Semester')->find($semesterId);
        $capacity = $this->getDoctrine()->getRepository('AppBundle:SchoolCapacity')->findBySchoolAndSemester($school, $semester);

        $form = $this->createForm(new SchoolCapacityType(), $capacity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($capacity);
            $em->flush();

            return $this->redirect($this->generateUrl('school_allocation'));
        }
        return $this->render('school_admin/school_allocate_edit.html.twig', array(
            'capacity' => $capacity,
            'form' => $form->createView(),
        ));
    }

    public function getName()
    {
        return 'SchoolAllocation'; // This must be unique
    }

}