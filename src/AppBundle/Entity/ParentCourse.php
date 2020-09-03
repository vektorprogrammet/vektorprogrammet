<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ParentCourse
 *
 * @ORM\Table(name="parent_course")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\ParentCourseRepository")
 */
class ParentCourse
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="Speaker", type="string", length=255, nullable=False)
     */
    private $speaker;

    /**
     * @var string
     *
     * @ORM\Column(name="Place", type="string", length=255, nullable=False)
     */
    private $place;

    /**
     * @var string
     *
     * @ORM\Column(name="Link", type="string", length=255, nullable=False)
     */
    private $link;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="Date", type="datetime", nullable=False)
     */
    private $date;

    /**
     * @var string
     *
     * @ORM\Column(name="Information", type="string", nullable=False)
     */
    private $information;

    /**
     * @var
     *
     * @ORM\OneToMany(targetEntity="ParentAssignment", mappedBy="course")
     */
    private $assignedParents;


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
     * Set speaker.
     *
     * @param string $speaker
     *
     * @return ParentCourse
     */
    public function setSpeaker($speaker)
    {
        $this->speaker = $speaker;

        return $this;
    }

    /**
     * Get speaker.
     *
     * @return string
     */
    public function getSpeaker()
    {
        return $this->speaker;
    }

    /**
     * Set place.
     *
     * @param string $place
     *
     * @return ParentCourse
     */
    public function setPlace($place)
    {
        $this->place = $place;

        return $this;
    }

    /**
     * Get place.
     *
     * @return string
     */
    public function getPlace()
    {
        return $this->place;
    }

    /**
     * Set link.
     *
     * @param string $link
     *
     * @return ParentCourse
     */
    public function setLink($link)
    {
        $this->link = $link;

        return $this;
    }

    /**
     * Get link.
     *
     * @return string
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * Set date.
     *
     * @param \DateTime $date
     *
     * @return ParentCourse
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date.
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set information.
     *
     * @param \string $information
     *
     * @return ParentCourse
     */
    public function setInformation($information)
    {
        $this->information = $information;

        return $this;
    }

    /**
     * Get information.
     *
     * @return \string
     */
    public function getInformation()
    {
        return $this->information;
    }

    /**
     * @return mixed
     */
    public function getAssignedParents()
    {
        return $this->assignedParents;
    }

    /**
     * @param mixed $assignedParents
     */
    public function setAssignedParents($assignedParents): void
    {
        $this->assignedParents = $assignedParents;
    }
}

