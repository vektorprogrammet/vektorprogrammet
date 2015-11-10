<?php
namespace AppBundle\Entity\SimulatedAnnealing;

class School
{
    private $name;
    private $bolk1Assistants;//An associative array. Key = weekday, Value = number of assistants. "Tuesday => 2
    private $bolk2Assistants;//An associative array. Key = weekday, Value = number of assistants. "Tuesday => 2
    private $capacity;//An associative array. Key = weekday, Value = capacity. "Wednesday" => 4.

    /**
     * School constructor.
     * @param capacity
     * @param name
     */
    public function __construct($capacity, $name)
    {
        $this->bolk1Assistants = array();
        $this->bolk2Assistants = array();
        $this->assistants = array();
        $this->capacity = $capacity;
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    public function deepCopy(){
        return $this;
        $copy = new School($this->capacity, $this->name);
        $copyAssistants = array();
        foreach($this->assistants as $day => $assistant){
            if(!array_key_exists($day, $copyAssistants)){
                 $copyAssistants[$day]=array();
            }
            foreach($assistant as $ass){
                $copyAssistants[$day][] = $ass->deepCopy($copy);
            }
        }
        $copy->setAssistants($copyAssistants);
        return $copy;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    public function hasAssistants($bolk2 = false){
        if(!$bolk2){
            $assistants = $this->bolk1Assistants;
        }else{
            $assistants = $this->bolk2Assistants;
        }
        if(sizeof($assistants) == 0)return false;
        foreach($assistants as $amount){
            if($amount > 0) {
                return true;
            }
        }
        return false;
    }

    public function addAssistant($day, $bolk2 = false){
        if(!$bolk2){
            if(!array_key_exists($day, $this->bolk1Assistants)){
                $this->bolk1Assistants[$day]= 1;
            }else{
                $this->bolk1Assistants[$day]++;
            }
        }else{
            if(!array_key_exists($day, $this->bolk2Assistants)){
                $this->bolk2Assistants[$day]= 1;
            }else{
                $this->bolk2Assistants[$day]++;
            }
        }
    }

    public function getAssistants($bolk2 = false){
        if(!$bolk2){
            return $this->bolk1Assistants;
        }else{
            return $this->bolk2Assistants;
        }
    }

    /**
     * @return array
     */
    public function getBolk1Assistants()
    {
        return $this->bolk1Assistants;
    }

    /**
     * @param array $bolk1Assistants
     */
    public function setBolk1Assistants($bolk1Assistants)
    {
        $this->bolk1Assistants = $bolk1Assistants;
    }

    /**
     * @return array
     */
    public function getBolk2Assistants()
    {
        return $this->bolk2Assistants;
    }

    /**
     * @param array $bolk2Assistants
     */
    public function setBolk2Assistants($bolk2Assistants)
    {
        $this->bolk2Assistants = $bolk2Assistants;
    }



    public function removeAssistant($day, $bolk2 = false){
        if(!$bolk2){
            if(array_key_exists($day, $this->bolk1Assistants) && $this->bolk1Assistants[$day] > 0) {
                $this->bolk1Assistants[$day] = $this->bolk1Assistants[$day] - 1;
            }
        }else{
            if(array_key_exists($day, $this->bolk2Assistants) && $this->bolk2Assistants[$day] > 0) {
                $this->bolk2Assistants[$day] = $this->bolk2Assistants[$day] -1;
            }
        }
    }

    /**
     * @return mixed
     */
    public function getCapacity()
    {
        return $this->capacity;
    }

    /**
     * @param mixed $capacity
     */
    public function setCapacity($capacity)
    {
        $this->capacity = $capacity;
    }



}