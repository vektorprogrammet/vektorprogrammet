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

    private function deepCopySchools($schools)
    {
        $copy = array();
        foreach ($schools as $school) {
            $copy[] = clone $school;
        }
        return $copy;
    }

    private function updateAssistantsInLockedList(Allocation $allocation, $lockedList)
    {
        foreach ($lockedList as $lockedAssistant) {
            $assistant = $allocation->getAssistantById($lockedAssistant->getId(), $allocation->getAssistants());
            $lockedAssistant->setAssignedSchool($assistant->getAssignedSchool());
            $lockedAssistant->setAssignedDay($assistant->getAssignedDay());

        }
    }

    private function assignAssistantsToBolks($assistants)
    {
        //Divide assistants into 'bolks'. If double position then assign to all three lists
        $assistantsInBolk1 = array();
        $assistantsInBolk2 = array();
        $lockedAssistants = array();
        $totalNumberOfAssistants = sizeof($assistants);
        //Assign assistants that has 'bolk' preferences
        foreach ($assistants as $assistant) {
            if ($assistant->isDoublePosition()) {
                $assistantsInBolk1[] = $assistant;
                $assistantCopy = clone $assistant;
                $assistantsInBolk2[] = $assistantCopy;
                $lockedAssistants[] = $assistant;
                $assistant->assignBothBolks();
            } elseif ($assistant->isPrefBolk1() && sizeof($assistantsInBolk1) < $totalNumberOfAssistants / 2) {
                $assistantsInBolk1[] = $assistant;
                $assistant->assignBolk1();
            } elseif ($assistant->isPrefBolk2() && sizeof($assistantsInBolk2) < $totalNumberOfAssistants / 2) {
                $assistantsInBolk2[] = $assistant;
                $assistant->assignBolk2();
            }
        }
        //Assign assistants with no 'bolk' preference
        foreach ($assistants as $assistant) {
            if ($assistant->isBolk1() || $assistant->isBolk2()) continue;
            if (sizeof($assistantsInBolk1) > sizeof($assistantsInBolk2)) {
                $assistantsInBolk2[] = $assistant;
                $assistant->assignBolk2();
            } else {
                $assistantsInBolk1[] = $assistant;
                $assistant->assignBolk1();
            }
        }

        return [$assistantsInBolk1, $assistantsInBolk2, $lockedAssistants];
    }

    private function generateSchoolsFromSchoolCapacities($schoolCapacities)
    {
        //Use schoolCapacities to create School objects for the SA-Algorithm
        $schools = array();
        foreach ($schoolCapacities as $sc) {
            if ($sc->getMonday() == 0 && $sc->getTuesday() == 0 && $sc->getWednesday() == 0 && $sc->getThursday() == 0 && $sc->getFriday() == 0) continue;
            $capacity = array();
            $capacity["Monday"] = $sc->getMonday();
            $capacity["Tuesday"] = $sc->getTuesday();
            $capacity["Wednesday"] = $sc->getWednesday();
            $capacity["Thursday"] = $sc->getThursday();
            $capacity["Friday"] = $sc->getFriday();

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
            $preferredGroup = $application->getPreferredGroup();
            $prefBolk1 = $preferredGroup == "Bolk 1";
            $prefBolk2 = $preferredGroup == "Bolk 2";
            if ($prefBolk1 && $prefBolk2) {
                $prefBolk1 = false;
                $prefBolk2 = false;
            }

            $availability = array();
            $availabilityPoints = ["Ikke", "Ok", "Bra"];
            $availability["Monday"] = array_search($application->getMonday(), $availabilityPoints);
            $availability["Tuesday"] = array_search($application->getTuesday(), $availabilityPoints);
            $availability["Wednesday"] = array_search($application->getWednesday(), $availabilityPoints);
            $availability["Thursday"] = array_search($application->getThursday(), $availabilityPoints);
            $availability["Friday"] = array_search($application->getFriday(), $availabilityPoints);

            $assistant = new Assistant();
            $assistant->setName($application->getUser()->getFirstName() . ' ' . $application->getUser()->getLastName());
            $assistant->setPrefBolk1($prefBolk1);
            $assistant->setPrefBolk2($prefBolk2);
            $assistant->setDoublePosition($doublePosition);
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

        $assistantsInBolksArray = $this->assignAssistantsToBolks($assistants);
        $assistantsInBolk1 = $assistantsInBolksArray[0];
        $assistantsInBolk2 = $assistantsInBolksArray[1];
        $lockedAssistants = $assistantsInBolksArray[2];

        //Create and find the initialAllocations (Very fast)
        $schoolsDeepCopy = $this->deepCopySchools($schools);
        $allocationBolk1 = new Allocation($schools, $assistantsInBolk1, true);
        $maxOptimizeTime = 2; //In seconds

        //Check if the initializer found the perfect allocation. If not, run the optimizer
        if ($allocationBolk1->isOk()) {
            $bestAllocationBolk1 = $allocationBolk1;
        } else {
            $optimizer = new Optimizer($allocationBolk1, 0.0001, 0.0000001, $maxOptimizeTime / 2);
            $bestAllocationBolk1 = $optimizer->optimize();
            $this->updateAssistantsInLockedList($bestAllocationBolk1, $lockedAssistants);
        }

        $allocationBolk2 = new Allocation($schoolsDeepCopy, $assistantsInBolk2, true, $lockedAssistants);

        if ($allocationBolk2->isOk()) {
            $bestAllocationBolk2 = $allocationBolk2;
        } else {
            $optimizer = new Optimizer($allocationBolk2, 0.0001, 0.0000001, $maxOptimizeTime / 2);
            $bestAllocationBolk2 = $optimizer->optimize();
        }

        //Total number of allocations evaluated during optimization
        $allocationsCount = Allocation::$visited;

        return $this->render('school_admin/school_allocate_result.html.twig', array(
            'initialAllocationBolk1' => $allocationBolk1,
            'initialAllocationBolk2' => $allocationBolk2,
            'score' => number_format((($allocationBolk1->evaluate() + $allocationBolk2->evaluate()) / 2), 1) * 10,
            'initializeTime' => $allocationBolk1->initializeTime + $allocationBolk1->improveTime + $allocationBolk2->initializeTime + $allocationBolk2->improveTime,
            'optimizeTime' => $bestAllocationBolk1->optimizeTime + $bestAllocationBolk2->optimizeTime,
            'optimizedAllocatedSchools' => $schools,
            'optimizedAllocationBolk1' => $bestAllocationBolk1,
            'optimizedAllocationBolk2' => $bestAllocationBolk2,
            'optimizedScore' => number_format((($bestAllocationBolk1->evaluate() + $bestAllocationBolk2->evaluate()) / 2), 1) * 10,
            'differentAllocations' => $allocationsCount,
        ));
        $response = new Response();
        $response->setContent(json_encode(array(
            'name' => "Kristoffer"
        )));
        return $response;
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

            return $this->redirect($this->generateUrl('allocate_schools'));
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