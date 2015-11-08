<?php
namespace AppBundle\Entity\SimulatedAnnealing;

class Assistant
{
    private $name;
    private $assignedSchool;//Name of school
    private $assignedDay;
    private $availability; //An associative array. Key = weekday, Value = {0, 1, 2}. 0 is bad, 1 is ok, 2 is good. "Monday" => 2.

    /**
     * Assistant constructor.
     */
    public function __construct()
    {
        $this->availability = array();
    }

    public function deepCopy($school = null){
        $copy = new Assistant();
        $copy->setName($this->name);
        if($school !== null){
            $copy->setAssignedSchool($school);
        }else{
            $copy->setAssignedSchool(clone $this->assignedSchool);
        }
        $copy->setAssignedDay($this->assignedDay);
        $copy->setAvailability($this->availability);
        return $copy;
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

    /**
     * @return array
     */
    public function getAvailability()
    {
        return $this->availability;
    }

    /**
     * @param array $availability
     */
    public function setAvailability($availability)
    {
        $this->availability = $availability;
    }


    /**
     * @return string
     */
    public function getAssignedSchool()
    {
        return $this->assignedSchool;
    }

    /**
     * @param string $assignedSchool
     */
    public function setAssignedSchool($assignedSchool)
    {
        $this->assignedSchool = $assignedSchool;
    }

    /**
     * @return mixed
     */
    public function getAssignedDay()
    {
        return $this->assignedDay;
    }

    /**
     * @param mixed $assignedDay
     */
    public function setAssignedDay($assignedDay)
    {
        $this->assignedDay = $assignedDay;
    }

    /**
     * @return array
     */
    public function getAvailable()
    {
        return $this->availability;
    }

    /**
     * @param array $availability
     */
    public function setAvailable($availability)
    {
        $this->availability = $availability;
    }



}