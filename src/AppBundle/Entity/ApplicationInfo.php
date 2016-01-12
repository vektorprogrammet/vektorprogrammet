<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\AdmissionRepository")
 * @ORM\Table(name="application_info")
 */
class ApplicationInfo {

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Semester")
     */
    protected $semester;

    /**
     * @ORM\Column(type="string" , length=20)
     */
    protected $yearOfStudy;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $monday;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $tuesday;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $wednesday;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $thursday;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $friday;

    /**
     * @ORM\Column(type="boolean", options={"default"=false})
     */
    protected $substitute;

    /**
     * @ORM\Column(type="boolean", options={"default"=false})
     */
    protected $english;

    /**
     * @ORM\Column(type="boolean", options={"default"=false})
     */
    protected $doublePosition;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $preferredGroup;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     */
    protected $user;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    protected $last_edited;

    /**
     * ApplicationInfo constructor.
     */
    public function __construct()
    {
        $this->last_edited = new \DateTime();
        $this->substitute = false;
        $this->english = false;
        $this->doublePosition = false;
    }


    /**
     * Get id
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return \AppBundle\Entity\Semester
     */
    public function getSemester()
    {
        return $this->semester;
    }

    /**
     * @param \AppBundle\Entity\Semester $semester
     */
    public function setSemester($semester)
    {
        $this->semester = $semester;
    }

    /**
     * @return mixed
     */
    public function getYearOfStudy()
    {
        return $this->yearOfStudy;
    }

    /**
     * @param mixed $yearOfStudy
     */
    public function setYearOfStudy($yearOfStudy)
    {
        $this->yearOfStudy = $yearOfStudy;
    }

    /**
     * @return mixed
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @param mixed $position
     */
    public function setPosition($position)
    {
        $this->position = $position;
    }

    /**
     * @return mixed
     */
    public function getMonday()
    {
        return $this->monday;
    }

    /**
     * @param mixed $monday
     */
    public function setMonday($monday)
    {
        $this->monday = $monday;
    }

    /**
     * @return mixed
     */
    public function getTuesday()
    {
        return $this->tuesday;
    }

    /**
     * @param mixed $tuesday
     */
    public function setTuesday($tuesday)
    {
        $this->tuesday = $tuesday;
    }

    /**
     * @return mixed
     */
    public function getWednesday()
    {
        return $this->wednesday;
    }

    /**
     * @param mixed $wednesday
     */
    public function setWednesday($wednesday)
    {
        $this->wednesday = $wednesday;
    }

    /**
     * @return mixed
     */
    public function getThursday()
    {
        return $this->thursday;
    }

    /**
     * @param mixed $thursday
     */
    public function setThursday($thursday)
    {
        $this->thursday = $thursday;
    }

    /**
     * @return mixed
     */
    public function getFriday()
    {
        return $this->friday;
    }

    /**
     * @param mixed $friday
     */
    public function setFriday($friday)
    {
        $this->friday = $friday;
    }

    /**
     * @return mixed
     */
    public function getSubstitute()
    {
        return $this->substitute;
    }

    /**
     * @param mixed $substitute
     */
    public function setSubstitute($substitute)
    {
        $this->substitute = $substitute;
    }

    /**
     * @return mixed
     */
    public function getEnglish()
    {
        return $this->english;
    }

    /**
     * @param mixed $english
     */
    public function setEnglish($english)
    {
        $this->english = $english;
    }

    /**
     * @return \AppBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param \AppBundle\Entity\User
     */
    public function setUser($user)
    {
        $this->user = $user;
    }



}
