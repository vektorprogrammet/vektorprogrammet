<?php

namespace AppBundle\Controller;

use AppBundle\Entity\SimulatedAnnealing\Assistant;
use AppBundle\Entity\SimulatedAnnealing\Node;
use AppBundle\Entity\SimulatedAnnealing\Optimizer;
use AppBundle\Entity\SimulatedAnnealing\School;
use AppBundle\Entity\SimulatedAnnealing\Solution;
use Doctrine\ORM\NoResultException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\SchoolCapacity;
use AppBundle\Entity\Interview;
use AppBundle\Form\Type\SchoolCapacityType;
use Symfony\Component\HttpFoundation\Response;

class SchoolAllocationController extends Controller
{

    public function showAction(Request $request, $departmentId=null){
        $user = $this->get('security.token_storage')->getToken()->getUser();

        if(null === $departmentId){
            $departmentId = $user->getFieldOfStudy()->getDepartment()->getId();
        }

        $allSemesters = $this->getDoctrine()->getRepository('AppBundle:Semester')->findByDepartment($departmentId);

        $currentSemester = null;
        foreach($allSemesters as $semester){
            $now = new \DateTime();
            if($semester->getSemesterStartDate() < $now && $semester->getSemesterEndDate() > $now){
                $currentSemester = $semester;
                break;
            }
        }

        $allCurrentSchoolCapacities = $this->getDoctrine()->getRepository('AppBundle:SchoolCapacity')->findBySemester($currentSemester);
        $allInterviews = $this->getDoctrine()->getRepository('AppBundle:Interview')->findAllInterviewedInterviewsBySemester($currentSemester);

        $assistants = $this->generateAssistantsFromInterviews($allInterviews, $currentSemester);
        $schools = $this->generateSchoolsFromSchoolCapacities($allCurrentSchoolCapacities);

        $assistantsInBolksArray = $this->assignAssistantsToBolks($assistants);
        $assistantsInBolk1 = $assistantsInBolksArray[0];
        $assistantsInBolk2 = $assistantsInBolksArray[1];
        $lockedAssistants = $assistantsInBolksArray[2];

        //Create and find the initialSolutions (Very fast)
        $schoolsDeepCopy = $this->deepCopySchools($schools);
        $solutionBolk1 = new Solution($schools, $assistantsInBolk1, true);

        $maxOptimizeTime = 100; //In seconds

        //Check if the initializer found the perfect solution. If not, run the optimizer
        if($solutionBolk1->isOk()){
            $bestSolutionBolk1 = $solutionBolk1;
        }else{
            $optimizer = new Optimizer($solutionBolk1, 0.0001, 0.0000001, $maxOptimizeTime/2);
            $bestSolutionBolk1 = $optimizer->optimize();
            $this->updateAssistantsInLockedList($bestSolutionBolk1, $lockedAssistants);
        }

        $solutionBolk2 = new Solution($schoolsDeepCopy, $assistantsInBolk2,true, $lockedAssistants);

        if($solutionBolk2->isOk()){
            $bestSolutionBolk2 = $solutionBolk2;
        }else{
            $optimizer = new Optimizer($solutionBolk2, 0.0001, 0.0000001, $maxOptimizeTime/2);
            $bestSolutionBolk2 = $optimizer->optimize();
        }

        //Total number of solutions evaluated during optimization
        $solutionsCount = Solution::$visited;

        return $this->render('school_admin/school_allocate_show.html.twig', array(
            'semester' => $currentSemester,
            'interviews' => $allInterviews,
            'allocations' => $allCurrentSchoolCapacities,
            'allocatedSchools' => $schools,
            /*'initialSolutionBolk1' => $solutionBolk1,
            'initialSolutionBolk2' => $solutionBolk2,
            'score' => number_format((($solutionBolk1->evaluate()+$solutionBolk2->evaluate())/2), 1)*10,
            'initializeTime' => $solutionBolk1->initializeTime + $solutionBolk1->improveTime + $solutionBolk2->initializeTime + $solutionBolk2->improveTime,
            'optimizeTime' => $bestSolutionBolk1->optimizeTime + $bestSolutionBolk2->optimizeTime,
            'optimizedAllocatedSchools' => $schools,
            'optimizedSolutionBolk1' => $bestSolutionBolk1,
            'optimizedSolutionBolk2' => $bestSolutionBolk2,
            'optimizedScore' => number_format((($bestSolutionBolk1->evaluate() + $bestSolutionBolk2->evaluate())/2), 1)*10,
            'differentSolutions' => $solutionsCount,*/
        ));
    }

    private function deepCopySchools($schools){
        $copy = array();
        foreach($schools as $school){
            $copy[] = clone $school;
        }
        return $copy;
    }

    private function updateAssistantsInLockedList(Solution $solution, $lockedList){
        foreach($lockedList as $lockedAssistant){
            $assistant = $solution->getAssistantById($lockedAssistant->getId(), $solution->getAssistants());
            $lockedAssistant->setAssignedSchool($assistant->getAssignedSchool());
            $lockedAssistant->setAssignedDay($assistant->getAssignedDay());

        }
    }

    private function assignAssistantsToBolks($assistants){
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
            }elseif($assistant->isPrefBolk1() && sizeof($assistantsInBolk1) < $totalNumberOfAssistants/2){
                $assistantsInBolk1[] = $assistant;
                $assistant->assignBolk1();
            }elseif($assistant->isPrefBolk2() && sizeof($assistantsInBolk2) < $totalNumberOfAssistants/2){
                $assistantsInBolk2[] = $assistant;
                $assistant->assignBolk2();
            }
        }
        //Assign assistants with no 'bolk' preference
        foreach ($assistants as $assistant) {
            if($assistant->isBolk1() || $assistant->isBolk2())continue;
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

    private function generateSchoolsFromSchoolCapacities($schoolCapacities){
        //Use schoolCapacities to create School objects for the SA-Algorithm
        $schools = array();
        foreach($schoolCapacities as $sc){
            if($sc->getMonday() == 0 && $sc->getTuesday() == 0 && $sc->getWednesday() == 0 && $sc->getThursday() == 0 && $sc->getFriday() == 0) continue;
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

    private function generateAssistantsFromInterviews($interviews, $semester){
        //Use interviews to create Assistant objects for the SA-algorithm
        $assistants = array();
        foreach($interviews as $interview) {
            $intPractical = $interview->getInterviewPractical();
            if($intPractical->getSemester() != $semester){
                continue;
            }

            $position = $intPractical->getPosition();
            $doublePosition = in_array("1x8", $position);
            $prefBolk1 = in_array("Bolk 1", $position);
            $prefBolk2 = in_array("Bolk 2", $position);
            if($prefBolk1 && $prefBolk2){
                $prefBolk1 = false;
                $prefBolk2 = false;
            }

            $preferredSchool = $intPractical->getPreferredSchool();
            if($preferredSchool !== null){
                $preferredSchool = $preferredSchool->getName();
            }

            $availability = array();
            $availabilityPoints = ["Ikke", "Ok", "Bra"];
            $availability["Monday"] = array_search($intPractical->getMonday(), $availabilityPoints);
            $availability["Tuesday"] = array_search($intPractical->getTuesday(), $availabilityPoints);
            $availability["Wednesday"] = array_search($intPractical->getWednesday(), $availabilityPoints);
            $availability["Thursday"] = array_search($intPractical->getThursday(), $availabilityPoints);
            $availability["Friday"] = array_search($intPractical->getFriday(), $availabilityPoints);

            $assistant = new Assistant();
            $assistant->setName($interview->getApplication()->getFirstName() . ' ' . $interview->getApplication()->getLastName());
            $assistant->setPrefBolk1($prefBolk1);
            $assistant->setPrefBolk2($prefBolk2);
            $assistant->setDoublePosition($doublePosition);
            $assistant->setPreferedSchool($preferredSchool);
            $assistant->setAvailability($availability);
            $assistants[] = $assistant;
        }
        return $assistants;
    }

    public function allocateAction(Request $request){
        $user = $this->get('security.token_storage')->getToken()->getUser();

        $departmentId = $user->getFieldOfStudy()->getDepartment()->getId();

        $allSemesters = $this->getDoctrine()->getRepository('AppBundle:Semester')->findByDepartment($departmentId);

        $currentSemester = null;
        foreach($allSemesters as $semester){
            $now = new \DateTime();
            if($semester->getSemesterStartDate() < $now && $semester->getSemesterEndDate() > $now){
                $currentSemester = $semester;
                break;
            }
        }

        $allCurrentSchoolCapacities = $this->getDoctrine()->getRepository('AppBundle:SchoolCapacity')->findBySemester($currentSemester);
        $allInterviews = $this->getDoctrine()->getRepository('AppBundle:Interview')->findAllInterviewedInterviewsBySemester($currentSemester);
        $assistants = $this->generateAssistantsFromInterviews($allInterviews, $currentSemester);
        $schools = $this->generateSchoolsFromSchoolCapacities($allCurrentSchoolCapacities);

        $assistantsInBolksArray = $this->assignAssistantsToBolks($assistants);
        $assistantsInBolk1 = $assistantsInBolksArray[0];
        $assistantsInBolk2 = $assistantsInBolksArray[1];
        $lockedAssistants = $assistantsInBolksArray[2];

        //Create and find the initialSolutions (Very fast)
        $schoolsDeepCopy = $this->deepCopySchools($schools);
        $solutionBolk1 = new Solution($schools, $assistantsInBolk1, true);
        $maxOptimizeTime = 1000; //In seconds

        //Check if the initializer found the perfect solution. If not, run the optimizer
        if($solutionBolk1->isOk()){
            $bestSolutionBolk1 = $solutionBolk1;
        }else{
            $optimizer = new Optimizer($solutionBolk1, 0.0001, 0.0000001, $maxOptimizeTime/2);
            $bestSolutionBolk1 = $optimizer->optimize();
            $this->updateAssistantsInLockedList($bestSolutionBolk1, $lockedAssistants);
        }

        $solutionBolk2 = new Solution($schoolsDeepCopy, $assistantsInBolk2,true, $lockedAssistants);

        if($solutionBolk2->isOk()){
            $bestSolutionBolk2 = $solutionBolk2;
        }else{
            $optimizer = new Optimizer($solutionBolk2, 0.0001, 0.0000001, $maxOptimizeTime/2);
            $bestSolutionBolk2 = $optimizer->optimize();
        }

        //Total number of solutions evaluated during optimization
        $solutionsCount = Solution::$visited;

        return $this->render('school_admin/school_allocate_result.html.twig', array(
            'initialSolutionBolk1' => $solutionBolk1,
            'initialSolutionBolk2' => $solutionBolk2,
            'score' => number_format((($solutionBolk1->evaluate()+$solutionBolk2->evaluate())/2), 1)*10,
            'initializeTime' => $solutionBolk1->initializeTime + $solutionBolk1->improveTime + $solutionBolk2->initializeTime + $solutionBolk2->improveTime,
            'optimizeTime' => $bestSolutionBolk1->optimizeTime + $bestSolutionBolk2->optimizeTime,
            'optimizedAllocatedSchools' => $schools,
            'optimizedSolutionBolk1' => $bestSolutionBolk1,
            'optimizedSolutionBolk2' => $bestSolutionBolk2,
            'optimizedScore' => number_format((($bestSolutionBolk1->evaluate() + $bestSolutionBolk2->evaluate())/2), 1)*10,
            'differentSolutions' => $solutionsCount,
        ));
        $response = new Response();
        $response->setContent(json_encode(array(
            'name' => "Kristoffer"
        )));
        return $response;
    }

    public function createAction(Request $request, $departmentId=null){
        $user = $this->get('security.token_storage')->getToken()->getUser();

        if(null === $departmentId){
            $departmentId = $user->getFieldOfStudy()->getDepartment()->getId();
        }
        $allSemesters = $this->getDoctrine()->getRepository('AppBundle:Semester')->findByDepartment($departmentId);
        $currentSemester = null;
        foreach($allSemesters as $semester){
            $now = new \DateTime();
            if($semester->getSemesterStartDate() < $now && $semester->getSemesterEndDate() > $now){
                $currentSemester = $semester;
                break;
            }
        }

        $schoolCapacity = new SchoolCapacity();
        $schoolCapacity->setSemester($currentSemester);
        $form = $this->createForm(new SchoolCapacityType(), $schoolCapacity);
        $form->handleRequest($request);

        if($form->isValid()){
            try{
                $exists = $this->getDoctrine()->getRepository('AppBundle:SchoolCapacity')->findBySchoolAndSemester($schoolCapacity->getSchool(), $schoolCapacity->getSemester());
                return $this->render('school_admin/school_allocate_create.html.twig', array(
                    'message' => 'Skolen eksisterer allerede',
                    'form' => $form->createView(),
                ));
            }catch (NoResultException $e){

            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($schoolCapacity);
            $em->flush();
            return $this->redirect($this->generateUrl('allocate_schools'));
        }

        return $this->render('school_admin/school_allocate_create.html.twig', array(
            'message' => '',
            'form' => $form->createView(),
        ));
    }
    public function editAction(Request $request){
        $schoolId = $request->query->get("school");
        $semesterId = $request->query->get("semester");
        $school = $this->getDoctrine()->getRepository('AppBundle:School')->find($schoolId);
        $semester = $this->getDoctrine()->getRepository('AppBundle:Semester')->find($semesterId);
        $capacity = $this->getDoctrine()->getRepository('AppBundle:SchoolCapacity')->findBySchoolAndSemester($school, $semester);

        $form = $this->createForm(new SchoolCapacityType(), $capacity);
        $form->handleRequest($request);

        if($form->isValid()){
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