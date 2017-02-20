<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\ApplicationRepository")
 * @ORM\Table(name="application")
 */
class Application
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Semester")
     */
    private $semester;

    /**
     * @ORM\Column(type="string" , length=20)
     * @Assert\NotBlank(groups={"admission", "admission_existing"}, message="Dette feltet kan ikke være tomt.")
     */
    private $yearOfStudy;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Assert\NotBlank(groups={"interview", "admission_existing"}, message="Dette feltet kan ikke være tomt.")
     */
    private $monday;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Assert\NotBlank(groups={"interview", "admission_existing"}, message="Dette feltet kan ikke være tomt.")
     */
    private $tuesday;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Assert\NotBlank(groups={"interview", "admission_existing"}, message="Dette feltet kan ikke være tomt.")
     */
    private $wednesday;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Assert\NotBlank(groups={"interview", "admission_existing"}, message="Dette feltet kan ikke være tomt.")
     */
    private $thursday;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Assert\NotBlank(groups={"interview", "admission_existing"}, message="Dette feltet kan ikke være tomt.")
     */
    private $friday;

    /**
     * @ORM\Column(type="boolean", options={"default"=false})
     */
    private $substitute;

    /**
     * @ORM\Column(type="boolean", options={"default"=false})
     * @Assert\NotBlank(groups={"interview", "admission_existing"}, message="Dette feltet kan ikke være tomt.")
     */
    private $english;

    /**
     * @ORM\Column(type="boolean", options={"default"=false})
     * @Assert\NotBlank(groups={"interview", "admission_existing"}, message="Dette feltet kan ikke være tomt.")
     */
    private $doublePosition;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $preferredGroup;

    /**
     * @ORM\ManyToOne(targetEntity="User", cascade={"persist"})
     * @Assert\Valid
     */
    private $user;

    /**
     * @ORM\Column(type="boolean")
     */
    private $previousParticipation;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $last_edited;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $created;

    /**
     * @ORM\Column(type="array")
     */
    private $heardAboutFrom;

    /**
     * @ORM\ManyToOne(targetEntity="Interview", cascade={"persist"})
     * @ORM\JoinColumn(name="interview_id", referencedColumnName="id", onDelete="SET NULL")
     * @Assert\Valid
     */
    private $interview;

    /**
     * ApplicationInfo constructor.
     */
    public function __construct()
    {
        $this->last_edited = new \DateTime();
        $this->created = new \DateTime();
        $this->substitute = false;
        $this->english = false;
        $this->doublePosition = false;
        $this->previousParticipation = false;
        $this->english = false;
    }

    /**
     * Get id.
     *
     * @return int
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
     * @return string
     */
    public function getYearOfStudy()
    {
        return $this->yearOfStudy;
    }

    /**
     * @param string $yearOfStudy
     */
    public function setYearOfStudy($yearOfStudy)
    {
        $this->yearOfStudy = $yearOfStudy;
    }

    /**
     * @return string
     */
    public function getMonday()
    {
        return $this->monday;
    }

    /**
     * @param string $monday
     */
    public function setMonday($monday)
    {
        $this->monday = $monday;
    }

    /**
     * @return string
     */
    public function getTuesday()
    {
        return $this->tuesday;
    }

    /**
     * @param string $tuesday
     */
    public function setTuesday($tuesday)
    {
        $this->tuesday = $tuesday;
    }

    /**
     * @return string
     */
    public function getWednesday()
    {
        return $this->wednesday;
    }

    /**
     * @param string $wednesday
     */
    public function setWednesday($wednesday)
    {
        $this->wednesday = $wednesday;
    }

    /**
     * @return string
     */
    public function getThursday()
    {
        return $this->thursday;
    }

    /**
     * @param string $thursday
     */
    public function setThursday($thursday)
    {
        $this->thursday = $thursday;
    }

    /**
     * @return string
     */
    public function getFriday()
    {
        return $this->friday;
    }

    /**
     * @param string $friday
     */
    public function setFriday($friday)
    {
        $this->friday = $friday;
    }

    /**
     * @return bool
     */
    public function getSubstitute()
    {
        return $this->substitute;
    }

    /**
     * @param bool $substitute
     */
    public function setSubstitute($substitute)
    {
        $this->substitute = $substitute;
    }

    /**
     * @return bool
     */
    public function getEnglish()
    {
        return $this->english;
    }

    /**
     * @param bool $english
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

    /**
     * @return bool
     */
    public function getDoublePosition()
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
     * @return string
     */
    public function getPreferredGroup()
    {
        return $this->preferredGroup;
    }

    /**
     * @param string $preferredGroup
     */
    public function setPreferredGroup($preferredGroup)
    {
        $this->preferredGroup = $preferredGroup;
    }

    /**
     * @return \DateTime
     */
    public function getLastEdited()
    {
        return $this->last_edited;
    }

    /**
     * @param \DateTime $last_edited
     */
    public function setLastEdited($last_edited)
    {
        $this->last_edited = $last_edited;
    }

    /**
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @param \DateTime $created
     */
    public function setCreated($created)
    {
        $this->created = $created;
    }

    /**
     * Set heardAboutFrom.
     *
     * @param array $heardAboutFrom
     */
    public function setHeardAboutFrom($heardAboutFrom)
    {
        $this->heardAboutFrom = $heardAboutFrom;
    }

    /**
     * Get heardAboutFrom.
     *
     * @return array
     */
    public function getHeardAboutFrom()
    {
        return $this->heardAboutFrom;
    }

    /**
     * @return Interview
     */
    public function getInterview()
    {
        return $this->interview;
    }

    /**
     * @param Interview $interview
     */
    public function setInterview($interview)
    {
        $this->interview = $interview;
    }

    /**
     * @return bool
     */
    public function getPreviousParticipation()
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
}
