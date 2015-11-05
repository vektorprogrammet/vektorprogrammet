<?php
namespace AppBundle\Entity\SimulatedAnnealing;

class School
{
    private $name;
    private $assistants;
    private $capacity;

    /**
     * School constructor.
     * @param capacity
     * @param name
     */
    public function __construct($capacity, $name)
    {
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

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }


    public function addAssistant($assistant, $day){
        if(!array_key_exists($day, $this->assistants)){
            $this->assistants[$day]=array();
        }
        array_push($this->assistants[$day], $assistant);
        $this->capacity[$day]--;
    }

    /**
     * @return mixed
     */
    public function getAssistants()
    {
        return $this->assistants;
    }

    /**
     * @param mixed $assistants
     */
    public function setAssistants($assistants)
    {
        $this->assistants = $assistants;
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

    public function decrementCapacity($day){
        $this->capacity[$day]--;
    }



}