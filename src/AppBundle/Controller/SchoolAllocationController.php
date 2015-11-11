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

class SchoolAllocationController extends Controller
{

    public function allocateAction(Request $request, $departmentId=null){
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

        $assistants = array();
        $schools = array();

        //Use interviews to create Assistant objects for the SA-algorithm
        foreach($allInterviews as $interview) {
            $intPractical = $interview->getInterviewPractical();
            if($intPractical->getSemester() != $currentSemester){
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
        //Use schoolCapacities to create School objects for the SA-Algorithm
        foreach($allCurrentSchoolCapacities as $sc){
            $capacity = array();
            $capacity["Monday"] = $sc->getMonday();
            $capacity["Tuesday"] = $sc->getTuesday();
            $capacity["Wednesday"] = $sc->getWednesday();
            $capacity["Thursday"] = $sc->getThursday();
            $capacity["Friday"] = $sc->getFriday();
            $school = new School($capacity, $sc->getSchool()->getName());

            $schools[] = $school;
        }

        $assistantsInBolk1 = array();
        $assistantsInBolk2 = array();
        $lockedAssistants = array();
        foreach ($assistants as $assistant) {
            if ($assistant->isDoublePosition()) {
                $assistantsInBolk1[] = $assistant;
                $assistantsInBolk2[] = clone $assistant;
                $lockedAssistants[] = $assistant;
                $assistant->assignBothBolks();
                continue;
            }elseif($assistant->isPrefBolk1()){
                $assistantsInBolk1[] = $assistant;
                $assistant->assignBolk1();
                continue;
            }elseif($assistant->isPrefBolk2()){
                $assistantsInBolk2[] = $assistant;
                $assistant->assignBolk2();
                continue;
            }
        }
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

        //Create and find the initialSolution (Very fast)
        $solutionBolk1 = new Solution($schools, $assistantsInBolk1);
        $solutionBolk2 = new Solution($this->deepCopySchools($schools), $assistantsInBolk2);
        $solutionBolk1->initializeSolution(true, true);
        $solutionBolk2->initializeSolution(true, true);
        //$solutionBolk1->improveSolution();
        //$solutionBolk2->improveSolution();
        $dcSolutionBolk1 = $solutionBolk1->deepCopy();
        $dcSolutionBolk2 = $solutionBolk2->deepCopy();

        $maxOptimizeTime = 20; //In seconds
        if($dcSolutionBolk1->evaluate() === 100){
            $solutionBolk1 = $dcSolutionBolk1;
            $bestSolutionBolk1 = $dcSolutionBolk1;
        }else{
            $optimizer = new Optimizer($solutionBolk1, 0.0001, 0.0000001, $maxOptimizeTime/2);
            $bestSolutionBolk1 = $optimizer->optimize();
        }
        if($dcSolutionBolk2->evaluate() === 100){
            $solutionBolk2 = $dcSolutionBolk2;
            $bestSolutionBolk2 = $dcSolutionBolk2;
        }else{
            $optimizer = new Optimizer($solutionBolk2, 0.0001, 0.0000001, $maxOptimizeTime/2);
            $bestSolutionBolk2 = $optimizer->optimize();
        }

        $solutionsCount = Solution::$visited;



        return $this->render('school_admin/school_allocate.html.twig', array(
            'interviews' => $allInterviews,
            'allocations' => $allCurrentSchoolCapacities,
            'allocatedSchools' => $schools,
            'initialSolutionBolk1' => $solutionBolk1,
            'initialSolutionBolk2' => $solutionBolk2,
            'score' => floor(($solutionBolk1->evaluate()+$solutionBolk2->evaluate())/2),
            'initializeTime' => $solutionBolk1->initializeTime + $solutionBolk1->improveTime + $solutionBolk2->initializeTime + $solutionBolk2->improveTime,
            'optimizeTime' => $bestSolutionBolk1->optimizeTime + $bestSolutionBolk2->optimizeTime,
            'optimizedAllocatedSchools' => $schools,
            'optimizedSolutionBolk1' => $bestSolutionBolk1,
            'optimizedSolutionBolk2' => $bestSolutionBolk2,
            'optimizedScore' => floor(($bestSolutionBolk1->evaluate() + $bestSolutionBolk2->evaluate())/2),
            'differentSolutions' => $solutionsCount,
        ));
    }

    private function deepCopySchools($schools){
        $copy = array();
        foreach($schools as $school){
            $copy[] = clone $school;
        }
        return $copy;
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
            dump("asd");
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