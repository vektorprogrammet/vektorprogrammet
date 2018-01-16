<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
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
     * @ORM\Column(type="string", length=250)
     * @Assert\NotBlank
     */
    private $date;

    /**
     * @ORM\Column(type="string", length=250)
     * @Assert\NotBlank
     */
    private $time;

    /**
     * @ORM\Column(type="string", length=250)
     * @Assert\NotBlank
     */
    private $room;

    /**
     * @ORM\Column(type="string", length=250)
     * @Assert\NotBlank
     */
    private $extra;

    /**
     * @ORM\OneToOne(targetEntity="Department", cascade={"persist"}, inversedBy="infoMeeting")
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     * @Assert\Valid
     */
    private $department;

    /**
     * @return mixed
     */
    public function getDepartment()
    {
        return $this->department;
    }

    /**
     * @param mixed $department
     */
    public function setDepartment($department)
    {
        $this->department = $department;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param mixed $date
     */
    public function setDate($date)
    {
        $this->date = $date;
    }

    /**
     * @return mixed
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * @param mixed $time
     */
    public function setTime($time)
    {
        $this->time = $time;
    }

    /**
     * @return mixed
     */
    public function getRoom()
    {
        return $this->room;
    }

    /**
     * @param mixed $room
     */
    public function setRoom($room)
    {
        $this->room = $room;
    }

    /**
     * @return mixed
     */
    public function getExtra()
    {
        return $this->extra;
    }

    /**
     * @param mixed $extra
     */
    public function setExtra($extra)
    {
        $this->extra = $extra;
    }

    public function __toString()
    {
        return "Husk infomøte ".$this->date." kl. ".$this->time." i ".$this->room.". ".$this->extra;
    }

}