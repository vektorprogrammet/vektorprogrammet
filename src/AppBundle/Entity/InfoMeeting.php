<?php

namespace AppBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Validator\Constraints as CustomAssert;

/**
 * @ORM\Table(name="infomeeting")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\InfoMeetingRepository")
 * @CustomAssert\InfoMeeting()
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
     * @var bool
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $showOnPage;

    /**
     * @ORM\Column(type="datetime", length=250, nullable=true)
     * @Assert\DateTime()
     */
    private $date;

    /**
     * @ORM\Column(type="string", length=250, nullable=true)
     * @Assert\Length(max=250)
     */
    private $room;

    /**
     * @ORM\Column(type="string", length=250, nullable=true)
     * @Assert\Length(max=250)
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=250, nullable=true)
     * @Assert\Length(max=250)
     */
    private $link;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param DateTime $date
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

    public function __toString()
    {
        return "Infomøte";
    }

    /**
     * @return bool
     */
    public function isShowOnPage()
    {
        return $this->showOnPage;
    }

    /**
     * @param bool $showOnPage
     */
    public function setShowOnPage($showOnPage)
    {
        $this->showOnPage = $showOnPage;
    }

    /**
     * @return string
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * @param string $link
     */
    public function setLink($link)
    {
        if (strlen($link) > 0 && substr($link, 0, 4) !== 'http') {
            $link = "http://$link";
        }

        $this->link = $link;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }
}
