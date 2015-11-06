<?php

namespace AppBundle\Controller;

use AppBundle\Entity\SimulatedAnnealing\Assistant;
use AppBundle\Entity\SimulatedAnnealing\Node;
use AppBundle\Entity\SimulatedAnnealing\School;
use AppBundle\Entity\SimulatedAnnealing\Solution;
use Doctrine\ORM\NoResultException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\SchoolCapacity;
use AppBundle\Entity\Interview;
use AppBundle\Form\Type\SchoolCapacityType;

class schoolAllocationController extends Controller
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
        $solution = new Solution($schools);
        $solution->initializeSolution($assistants);
        $node = new Node($solution);
        $neighbours = $node->generateNeighbours();
        foreach($neighbours as $neighbour){
            dump($neighbour->getSchools()[2]->getAssistants());
        }
        return $this->render('school_admin/school_allocate.html.twig', array(
            'interviews' => $allInterviews,
            'allocations' => $allCurrentSchoolCapacities,
            'allocatedSchools' => $solution->getSchools(),
            'score' => $solution->evaluate(),
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



            dump($schoolCapacity);
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
        return 'schoolAllocation'; // This must be unique
    }

}