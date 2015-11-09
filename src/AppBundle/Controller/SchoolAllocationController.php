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
            $assistant = new Assistant();
            $assistant->setName($interview->getApplication()->getFirstName() . ' ' . $interview->getApplication()->getLastName());
            $availability = array();
            $intPractical = $interview->getInterviewPractical();
            $availabilityPoints = ["Ikke", "Ok", "Bra"];
            $availability["Monday"] = array_search($intPractical->getMonday(), $availabilityPoints);
            $availability["Tuesday"] = array_search($intPractical->getTuesday(), $availabilityPoints);
            $availability["Wednesday"] = array_search($intPractical->getWednesday(), $availabilityPoints);
            $availability["Thursday"] = array_search($intPractical->getThursday(), $availabilityPoints);
            $availability["Friday"] = array_search($intPractical->getFriday(), $availabilityPoints);
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

        //Create and find the initialSolution (Very fast)
        $solution = new Solution($schools,$assistants);
        $dcSolution = $solution->deepCopy();
        $dcSolution->initializeSolution(true, true);
        $dcSolution->improveSolution();
        if($dcSolution->evaluate() === 100){
            $solution = $dcSolution;
            $bestSolution = $dcSolution;
        }else{
            $solution->initializeSolution(true, false);
            $solution->improveSolution();
            //Optimize the initialized solution (Very slow)
            $node = new Node($solution);
            $optimizer = new Optimizer($node, 0.0001, 0.0000001);
            $bestSolution = $optimizer->optimize();
        }
        $solutionsCount = Solution::$visited;


        return $this->render('school_admin/school_allocate.html.twig', array(
            'interviews' => $allInterviews,
            'allocations' => $allCurrentSchoolCapacities,
            'allocatedSchools' => $solution->getSchools(),
            'allocatedAssistants' => $solution->getAssistants(),
            'score' => $solution->evaluate(),
            'initializeTime' => $solution->initializeTime + $solution->improveTime,
            'optimizeTime' => $bestSolution->optimizeTime,
            'optimizedAllocatedSchools' => $bestSolution->getSchools(),
            'optimizedAllocatedAssistants' => $bestSolution->getAssistants(),
            'optimizedScore' => $bestSolution->evaluate(),
            'differentSolutions' => $solutionsCount,
        ));
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