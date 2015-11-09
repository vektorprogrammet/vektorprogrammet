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
                    if($capacity === 0) continue;
                    //Check if the school has capacity for more assistants
                    $cap = array_key_exists($day, $school->getAssistants()) ? $school->getAssistants()[$day] : 0;
                    $freeCapacity = $capacity - $cap;
                    if($freeCapacity < 1) continue;

                    //Create a deep copy of the current solution
                    $schoolsCopy = $this->solution->deepCopySchools();
                    $assistantsCopy = $this->solution->deepCopyAssistants($schoolsCopy);
                    $newSolution = new Solution($schoolsCopy, $assistantsCopy);
                    //Move the assistant from current school to a new school and add the new solution to the neighbour list
                    $newSolution->moveAssistant($assistantsCopy[$assistantIndex], $schoolsCopy[$schoolIndex]->getName(), $assistant->getAssignedDay(), $day);
                    /*if(sizeof(Solution::$visited) > 0 && in_array($newSolution, Solution::$visited)){
                        continue;
                    }*/
                    Solution::$visited++;
                    $node = new Node($newSolution);
                    $neighbours[] = $node;
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