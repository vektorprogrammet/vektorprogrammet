<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="infomeeting")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\InfoMeetingRepository")
 */
class InfoMeeting
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime", length=250)
     * @Assert\NotBlank
     * @Assert\DateTime()
     */
    private $date;

    /**
     * @ORM\Column(type="string", length=250)
     * @Assert\NotBlank
     * @Assert\Length(max=250)
     */
    private $room;

    /**
     * @ORM\Column(type="string", length=250)
     * @Assert\NotBlank
     * @Assert\Length(max=250)
     */
    private $extra;

    /**
     * @ORM\OneToOne(targetEntity="Department", cascade={"persist"}, inversedBy="infoMeeting")
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     * @Assert\Valid
     */
    private $department;

    /**
     * @return Department
     */
    public function getDepartment()
    {
        return $this->department;
    }

    /**
     * @param Department $department
     */
    public function setDepartment($department)
    {
        $this->department = $department;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param \DateTime $date
     */
    public function setDate($date)
    {
        $this->date = $date;
    }

    /**
     * @return string
     */
    public function getRoom()
    {
        return $this->room;
    }

    /**
     * @param string $room
     */
    public function setRoom($room)
    {
        $this->room = $room;
    }

    /**
     * @return string
     */
    public function getExtra()
    {
        return $this->extra;
    }

    /**
     * @param string $extra
     */
    public function setExtra($extra)
    {
        $this->extra = $extra;
    }

    public function __toString()
    {
        return "Infomøte " . $this->getDepartment();
    }
}
