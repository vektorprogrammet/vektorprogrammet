<?php

namespace AppBundle\Model;

use AppBundle\Entity\School;
use AppBundle\Entity\Term;

/**
 * AssistantDelegationInfo
 */
class AssistantDelegationInfo
{

    /**
     * @var School
     */
    private $school;

    /**
     * @var Term
     */
    private $term;

    /**
     * @var integer
     */
    private $weekDay;

    /**
     * @var boolean
     */
    private $doublePosition;

    /**
     * @return School
     */
    public function getSchool(): School
    {
        return $this->school;
    }

    /**
     * @param School $school
     *
     * @return AssistantDelegationInfo
     */
    public function setSchool(School $school): AssistantDelegationInfo
    {
        $this->school = $school;
        return $this;
    }

    /**
     * @return Term
     */
    public function getTerm(): Term
    {
        return $this->term;
    }

    /**
     * @param Term $term
     *
     * @return AssistantDelegationInfo
     */
    public function setTerm(Term $term): AssistantDelegationInfo
    {
        $this->term = $term;
        return $this;
    }

    /**
     * Set weekDay
     *
     * @param integer $weekDay
     *
     * @return AssistantDelegationInfo
     */
    public function setWeekDay($weekDay)
    {
        $this->weekDay = $weekDay;

        return $this;
    }

    /**
     * Get weekDay
     *
     * @return integer 
     */
    public function getWeekDay()
    {
        return $this->weekDay;
    }

    /**
     * Set doublePosition
     *
     * @param boolean $doublePosition
     * @return AssistantDelegationInfo
     */
    public function setDoublePosition($doublePosition)
    {
        $this->doublePosition = $doublePosition;

        return $this;
    }

    /**
     * Get doublePosition
     *
     * @return boolean 
     */
    public function getDoublePosition()
    {
        return $this->doublePosition;
    }
}
