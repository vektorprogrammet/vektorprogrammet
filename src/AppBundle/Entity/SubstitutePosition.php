<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SubstitutePosition
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\SubstitutePositionRepository")
 */
class SubstitutePosition
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="comment", type="string", length=5000, nullable=true)
     */
    private $comment;

    /**
     * @var Semester
     *
     * @ORM\ManyToOne(targetEntity="Semester")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $semester;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="User", inversedBy="substitutePositions")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $user;

    /**
     * @var boolean
     * @ORM\Column(name="monday", type="boolean")
     */
    private $monday;

    /**
     * @var boolean
     * @ORM\Column(name="tuesday", type="boolean")
     */
    private $tuesday;

    /**
     * @var boolean
     * @ORM\Column(name="wednesday", type="boolean")
     */
    private $wednesday;

    /**
     * @var boolean
     * @ORM\Column(name="thursday", type="boolean")
     */
    private $thursday;

    /**
     * @var boolean
     * @ORM\Column(name="friday", type="boolean")
     */
    private $friday;

    /**
     * @var PartnerWorkDay[]
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\PartnerWorkDay", inversedBy="substitutePositions")
     */
    private $partnerWorkDays;

    /**
     * SubstitutePosition constructor.
     */
    public function __construct()
    {
        $this->monday = true;
        $this->tuesday = true;
        $this->wednesday = true;
        $this->thursday = true;
        $this->friday = true;
    }

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
     * Set comment
     *
     * @param string $comment
     * @return SubstitutePosition
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get comment
     *
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Get semester
     *
     * @return Semester
     */
    public function getSemester(): ?Semester
    {
        return $this->semester;
    }

    /**
     * Set semester
     *
     * @param Semester $semester
     *
     * @return SubstitutePosition
     */
    public function setSemester(Semester $semester): SubstitutePosition
    {
        $this->semester = $semester;
        return $this;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     *
     * @return SubstitutePosition
     */
    public function setUser(User $user): SubstitutePosition
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @return bool
     */
    public function isMonday(): bool
    {
        return $this->monday;
    }

    /**
     * @param bool $monday
     *
     * @return SubstitutePosition
     */
    public function setMonday(bool $monday): SubstitutePosition
    {
        $this->monday = $monday;
        return $this;
    }

    /**
     * @return bool
     */
    public function isTuesday(): bool
    {
        return $this->tuesday;
    }

    /**
     * @param bool $tuesday
     *
     * @return SubstitutePosition
     */
    public function setTuesday(bool $tuesday): SubstitutePosition
    {
        $this->tuesday = $tuesday;
        return $this;
    }

    /**
     * It isWednesday my dudes
     *
     * @return bool
     */
    public function isWednesday(): bool
    {
        return $this->wednesday;
    }

    /**
     * @param bool $wednesday
     *
     * @return SubstitutePosition
     */
    public function setWednesday(bool $wednesday): SubstitutePosition
    {
        $this->wednesday = $wednesday;
        return $this;
    }

    /**
     * @return bool
     */
    public function isThursday(): bool
    {
        return $this->thursday;
    }

    /**
     * @param bool $thursday
     *
     * @return SubstitutePosition
     */
    public function setThursday(bool $thursday): SubstitutePosition
    {
        $this->thursday = $thursday;
        return $this;
    }

    /**
     * @return bool
     */
    public function isFriday(): bool
    {
        return $this->friday;
    }

    /**
     * @param bool $friday
     *
     * @return SubstitutePosition
     */
    public function setFriday(bool $friday): SubstitutePosition
    {
        $this->friday = $friday;
        return $this;
    }

    /**
     * @return PartnerWorkDay[]
     */
    public function getPartnerWorkDays()
    {
        return $this->partnerWorkDays;
    }

    /**
     * @param PartnerWorkDay[] $partnerWorkDays
     *
     * @return SubstitutePosition
     */
    public function setPartnerWorkDays(array $partnerWorkDays): SubstitutePosition
    {
        $this->partnerWorkDays = $partnerWorkDays;
        return $this;
    }

    /**
     * @param PartnerWorkDay $partnerWorkDay
     *
     * @return SubstitutePosition
     */
    public function addPartnerWorkDay(PartnerWorkDay $partnerWorkDay): SubstitutePosition
    {
        $this->partnerWorkDays[] = $partnerWorkDay;
        return $this;
    }
}
