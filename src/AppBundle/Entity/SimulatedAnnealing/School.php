<?php
namespace AppBundle\Entity\SimulatedAnnealing;

class School
{
    private $name;
    private $assistants;//An associative array. Key = weekday, Value = number of assistants. "Tuesday => 2
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
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    public function hasAssistants(){
        $assistants = $this->assistants;

        if(sizeof($assistants) == 0)return false;
        foreach($assistants as $amount){
            if($amount > 0) {
                return true;
            }
        }
        return false;
    }

    public function totalAssistants(){
        $assistants = 0;
        foreach ($this->assistants as $amount) {
            $assistants += $amount;
        }
        return $assistants;

    }

    public function capacityOnDay($day){
        if(array_key_exists($day, $this->assistants)) return $this->capacity[$day];
        return ($this->capacity[$day] - $this->assistants[$day]);
    }

    public function addAssistant($day){
        if(!array_key_exists($day, $this->assistants)){
            $this->assistants[$day]= 1;
        }else{
            $this->assistants[$day]++;
        }
    }

    public function getAssistants(){
        return $this->assistants;
    }



    public function removeAssistant($day){
        if(array_key_exists($day, $this->assistants) && $this->assistants[$day] > 0) {
            $this->assistants[$day] = $this->assistants[$day] - 1;
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