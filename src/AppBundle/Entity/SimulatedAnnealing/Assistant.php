<?php
namespace AppBundle\Entity\SimulatedAnnealing;

class Assistant
{
    private $name;
    private $assignedSchool;//Name of school
    private $assignedDay;
    private $availability; //An associative array. Key = weekday, Value = {0, 1, 2}. 0 is bad, 1 is ok, 2 is good. "Monday" => 2.
    private $bolk1, $bolk2;
    private $prefBolk1, $prefBolk2;
    private $preferredSchool;
    private $doublePosition;

    /**
     * Assistant constructor.
     */
    public function __construct()
    {
        $this->bolk1 = false;
        $this->bolk2 = false;
        $this->prefBolk1 = false;
        $this->prefBolk2 = false;
        $this->doublePosition = false;
        $this->preferredSchool = null;
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
     * @return null
     */
    public function getPreferedSchool()
    {
        return $this->preferedSchool;
    }

    /**
     * @param null $preferredSchool
     */
    public function setPreferedSchool($preferredSchool)
    {
        $this->preferredSchool = $preferredSchool;
    }

    /**
     * @return boolean
     */
    public function isPrefBolk1()
    {
        return $this->prefBolk1;
    }

    /**
     * @param boolean $prefBolk1
     */
    public function setPrefBolk1($prefBolk1)
    {
        $this->prefBolk1 = $prefBolk1;
    }

    /**
     * @return boolean
     */
    public function isPrefBolk2()
    {
        return $this->prefBolk2;
    }

    /**
     * @param boolean $prefBolk2
     */
    public function setPrefBolk2($prefBolk2)
    {
        $this->prefBolk2 = $prefBolk2;
    }

    /**
     * @return null
     */
    public function getPreferredSchool()
    {
        return $this->preferredSchool;
    }

    /**
     * @param null $preferredSchool
     */
    public function setPreferredSchool($preferredSchool)
    {
        $this->preferredSchool = $preferredSchool;
    }



    /**
     * @return boolean
     */
    public function isDoublePosition()
    {
        return $this->doublePosition;
    }

    /**
     * @param boolean $doublePosition
     */
    public function setDoublePosition($doublePosition)
    {
        $this->doublePosition = $doublePosition;
    }



    /**
     * @return boolean
     */
    public function isBolk1()
    {
        return $this->bolk1;
    }

    /**
     *
     */
    public function assignBolk1()
    {
        $this->bolk2 = false;
        $this->bolk1 = true;
    }

    /**
     * @return boolean
     */
    public function isBolk2()
    {
        return $this->bolk2;
    }

    /**
     *
     */
    public function assignBolk2()
    {
        $this->bolk1 = false;
        $this->bolk2 = true;
    }

    public function assignBothBolks(){
        $this->bolk2 = true;
        $this->bolk1 = true;
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