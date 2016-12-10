<?php

namespace AppBundle\SchoolAllocation;

class Assistant
{
    public static $idCounter;
    /**
     * @var int
     */
    private $id;
    /**
     * @var string
     */
    private $name;
    /**
     * @var string
     */
    private $assignedSchool; //Name of school
    /**
     * @var string
     */
    private $assignedDay;
    /**
     * @var array
     */
    private $availability; //An associative array. Key = weekday, Value = {0, 1, 2}. 0 is bad, 1 is ok, 2 is good. "Monday" => 2.
    /**
     * @var int
     */
    private $group;
    /**
     * @var int
     */
    private $preferredGroup;
    /**
     * @var bool
     */
    private $doublePosition;

    /**
     * @var bool
     */
    private $previousParticipation;

    /**
     * @var int
     */
    private $score;

    /**
     * @var string
     */
    private $suitability;

    /**
     * Assistant constructor.
     */
    public function __construct()
    {
        if (self::$idCounter === null) {
            $this->id = 1;
            self::$idCounter = 1;
        } else {
            $this->id = ++self::$idCounter;
        }
        $this->group = null;
        $this->preferredGroup = null;
        $this->doublePosition = false;
        $this->availability = array();
    }

    /**
     * @return bool
     */
    public function isAssignedToSchool()
    {
        return !is_null($this->assignedSchool);
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return bool
     */
    public function isDoublePosition()
    {
        return $this->doublePosition;
    }

    /**
     * @param bool $doublePosition
     */
    public function setDoublePosition($doublePosition)
    {
        $this->doublePosition = $doublePosition;
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
     * @return string
     */
    public function getAssignedDay()
    {
        return $this->assignedDay;
    }

    /**
     * @param string $assignedDay
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

    /**
     * @return int
     */
    public function getPreferredGroup()
    {
        return $this->preferredGroup;
    }

    /**
     * @param int $preferredGroup
     */
    public function setPreferredGroup($preferredGroup)
    {
        $this->preferredGroup = $preferredGroup;
    }

    /**
     * @return int
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * @param int $group
     */
    public function setGroup($group)
    {
        $this->group = $group;
    }

    /**
     * @return bool
     */
    public function isPreviousParticipation()
    {
        return $this->previousParticipation;
    }

    /**
     * @param bool $previousParticipation
     */
    public function setPreviousParticipation($previousParticipation)
    {
        $this->previousParticipation = $previousParticipation;
    }

    /**
     * @return int
     */
    public function getScore()
    {
        return $this->score;
    }

    /**
     * @param int $score
     */
    public function setScore($score)
    {
        $this->score = $score;
    }

    /**
     * @return string
     */
    public function getSuitability()
    {
        return $this->suitability;
    }

    /**
     * @param string $suitability
     */
    public function setSuitability($suitability)
    {
        $this->suitability = $suitability;
    }

    /**
     * @param School $school
     * @param int    $group
     * @param string $day
     */
    public function assignToSchool(School $school, $group, $day)
    {
        $this->setAssignedSchool($school->getName());
        if ($this->group == 1 && $group == 2 || $this->group == 2 && $group == 1) {
            $this->group = 3;
        } else {
            $this->setGroup($group);
        }
        $this->setAssignedDay($day);
    }
}
