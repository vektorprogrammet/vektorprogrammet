<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\AdmissionRepository")
 * @ORM\Table(name="Application")
 */
class Application {

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
	
	/**
     * @ORM\Column(type="string", length=250)
     */
    protected $firstName;
	
	/**
     * @ORM\Column(type="string", length=250)
     */
    protected $lastName;
	
	/**
     * @ORM\Column(type="string", length=250)
     */
    protected $phone;
	
	/**
     * @ORM\Column(type="string", length=250)
     */
    protected $email;
	
	/**
     * @ORM\Column(type="boolean")
     */
    protected $userCreated;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $substituteCreated;

	/**
     * @ORM\OneToOne(targetEntity="ApplicationStatistic", cascade={"persist"})
     **/
	 protected $statistic;

    /**
     * @ORM\OneToOne(targetEntity="Interview", mappedBy="application", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    protected $interview;
	

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set firstName
     *
     * @param string $firstName
     * @return Application
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get firstName
     *
     * @return string 
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     * @return Application
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get lastName
     *
     * @return string 
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set phone
     *
     * @param string $phone
     * @return Application
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string 
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return Application
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set userCreated
     *
     * @param boolean $userCreated
     * @return Application
     */
    public function setUserCreated($userCreated)
    {
        $this->userCreated = $userCreated;

        return $this;
    }

    /**
     * Get userCreated
     *
     * @return boolean 
     */
    public function getUserCreated()
    {
        return $this->userCreated;
    }

    /**
     * Set statistic
     *
     * @param \AppBundle\Entity\ApplicationStatistic $statistic
     * @return Application
     */
    public function setStatistic(\AppBundle\Entity\ApplicationStatistic $statistic = null)
    {
        $this->statistic = $statistic;

        return $this;
    }

    /**
     * Get statistic
     *
     * @return \AppBundle\Entity\ApplicationStatistic
     */
    public function getStatistic()
    {
        return $this->statistic;
    }

    /**
     * Set interview
     *
     * @param \AppBundle\Entity\Interview $interview
     * @return Application
     */
    public function setInterview(\AppBundle\Entity\Interview $interview = null)
    {
        // Must also set the owning side as it is the one doctrine watches
        $interview->setApplication($this);

        $this->interview = $interview;

        return $this;
    }

    /**
     * Get interview
     *
     * @return \AppBundle\Entity\Interview
     */
    public function getInterview()
    {
        return $this->interview;
    }

    /**
     * Does the given User belong to the same department as this Application?
     *
     * @param User $user
     * @return boolean
     */
    public function isSameDepartment(User $user = null)
    {
        return $user && $user->getFieldOfStudy()->getDepartment()->getId() == $this->getStatistic()->getSemester()->getDepartment()->getId();
    }

    /**
     * Set substituteCreated
     *
     * @param boolean $substituteCreated
     * @return Application
     */
    public function setSubstituteCreated($substituteCreated)
    {
        $this->substituteCreated = $substituteCreated;

        return $this;
    }

    /**
     * Get substituteCreated
     *
     * @return boolean 
     */
    public function getSubstituteCreated()
    {
        return $this->substituteCreated;
    }
}
