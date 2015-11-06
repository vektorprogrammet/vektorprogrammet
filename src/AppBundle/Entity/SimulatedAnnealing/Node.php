<?php


namespace AppBundle\Entity\SimulatedAnnealing;


class Node
{
    private $solution;

    /**
     * Node constructor.
     * @param $solution
     */
    public function __construct(Solution $solution)
    {
        $this->solution = $solution;
    }


    public function generateNeighbours(){
        $neighbours = array();
        $assistantIndex = 0;
        foreach($this->solution->getAssistants() as $assistant){
            $schoolIndex = 0;
            foreach($this->solution->getSchools() as $school){
                foreach($school->getCapacity() as $day=>$capacity){
                    $cap = array_key_exists($day, $school->getAssistants()) ? sizeof($school->getAssistants()[$day]) : 0;
                    $freeCapacity = $capacity - $cap;
                    if($freeCapacity < 1) continue;
                    $schoolsCopy = $this->solution->deepCopySchools();
                    $assistantsCopy = $this->solution->deepCopyAssistants($schoolsCopy);
                    $newSolution = new Solution($schoolsCopy);
                    $newSolution->setAssistants($assistantsCopy);
                    $newSolution->moveAssistant($assistantsCopy[$assistantIndex], $schoolsCopy[$schoolIndex], $assistant->getAssignedDay(), $day);
                    $neighbours[] = new Node($newSolution);
                }

                $schoolIndex++;
            }
            $assistantIndex++;
        }
        return $neighbours;
    }

    /**
     * @return Solution
     */
    public function getSolution()
    {
        return $this->solution;
    }

    /**
     * @param Solution $solution
     */
    public function setSolution($solution)
    {
        $this->solution = $solution;
    }


}