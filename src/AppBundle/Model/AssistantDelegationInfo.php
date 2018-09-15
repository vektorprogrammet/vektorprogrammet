<?php

namespace AppBundle\Model;

use AppBundle\Entity\School;
use AppBundle\Entity\User;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * AssistantDelegationInfo
 */
class AssistantDelegationInfo
{

    /**
     * @var User
     */
    private $user;

    /**
     * @var School
     * @Assert\NotNull(message="Dette feltet kan ikke være tomt")
     * @Assert\Valid()
     */
    private $school;

    /**
     * @var integer
     * @Assert\NotNull(message="Dette feltet kan ikke være tomt")
     * @Assert\Range(min=1, max=7,
     *     minMessage="Du må velge en dag mellom mandag og fredag",
     *     maxMessage="Du må velge en dag mellom mandag og fredag")
     * @Assert\Valid()
     */
    private $weekDay;

    /**
     * @var integer
     * @Assert\GreaterThan(value=0, message="Det der gir ikke mening")
     * @Assert\Valid()
     * @Assert\NotNull(message="Dette feltet kan ikke være tomt")
     */
    private $numDays;

    /**
     * @var integer
     * @Assert\Range(min=1, max=52,
     *     minMessage="Ukenummer kan ikke være mindre enn 1",
     *     maxMessage="Ukenummer kan ikke være mer enn 52")
     * @Assert\Valid()
     * @Assert\NotNull(message="Dette feltet kan ikke være tomt")
     */
    private $startingWeek;

    /**
     * @var boolean
     */
    private $term1;

    /**
     * @var boolean
     */
    private $term2;

    /**
     * @return User
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * @param User $user
     *
     * @return AssistantDelegationInfo
     */
    public function setUser(User $user): AssistantDelegationInfo
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @return School
     */
    public function getSchool(): ?School
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
     * @return int
     */
    public function getNumDays(): ?int
    {
        return $this->numDays;
    }

    /**
     * @param int $numDays
     *
     * @return AssistantDelegationInfo
     */
    public function setNumDays(int $numDays): AssistantDelegationInfo
    {
        $this->numDays = $numDays;
        return $this;
    }

    /**
     * @return int
     */
    public function getStartingWeek(): ?int
    {
        return $this->startingWeek;
    }

    /**
     * @param int $startingWeek
     *
     * @return AssistantDelegationInfo
     */
    public function setStartingWeek(int $startingWeek): AssistantDelegationInfo
    {
        $this->startingWeek = $startingWeek;
        return $this;
    }

    /**
     * @return bool
     */
    public function isTerm1(): bool
    {
        return $this->term1;
    }

    /**
     * @param bool $term1
     *
     * @return AssistantDelegationInfo
     */
    public function setTerm1(bool $term1): AssistantDelegationInfo
    {
        $this->term1 = $term1;
        return $this;
    }

    /**
     * @return bool
     */
    public function isTerm2(): bool
    {
        return $this->term2;
    }

    /**
     * @param bool $term2
     *
     * @return AssistantDelegationInfo
     */
    public function setTerm2(bool $term2): AssistantDelegationInfo
    {
        $this->term2 = $term2;
        return $this;
    }
}
