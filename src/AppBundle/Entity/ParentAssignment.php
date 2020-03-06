<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use AppBundle\Entity\ParentCourse;

/**
 * ParentAssignment
 *
 * @ORM\Table(name="parent_assignment")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\ParentAssignmentRepository")
 */
class ParentAssignment
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
     * @ORM\Column(name="Navn", type="string", length=255, nullable=False)
     */
    private $navn;

    /**
     * @var string
     *
     * @ORM\Column(name="Epost", type="string", length=255, nullable=False)
     */
    private $epost;

    /**
     * @var ParentCourse
     *
     * @ORM\ManyToOne(targetEntity="ParentCourse", inversedBy="assignedParents")
     */
    private $course;

    /**
     * @var string
     *
     * @ORM\Column(name="UniqueKey", type="string", nullable=True)
     */
    private $uniqueKey;


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
     * Set navn.
     *
     * @param string $navn
     *
     * @return ParentAssignment
     */
    public function setNavn($navn)
    {
        $this->navn = $navn;

        return $this;
    }

    /**
     * Get navn.
     *
     * @return string
     */
    public function getNavn()
    {
        return $this->navn;
    }

    /**
     * Set epost.
     *
     * @param string $epost
     *
     * @return ParentAssignment
     */
    public function setEpost($epost)
    {
        $this->epost = $epost;

        return $this;
    }

    /**
     * Get epost.
     *
     * @return string
     */
    public function getEpost()
    {
        return $this->epost;
    }


    /**
     * Set course.
     *
     * @param ParentCourse $course
     *
     * @return void
     */
    public function setCourse(ParentCourse $course): void
    {
        $this->course = $course;
    }

    /**
     * Get course.
     *
     * @return ParentCourse
     */
    public function getCourse(): ParentCourse
    {
        return $this->course;
    }

    /**
     * Set uniqueId.
     *
     * @param string $uniqueKey
     *
     * @return ParentAssignment
     */
    public function setUniqueKey(string $uniqueKey): void
    {
        $this->uniqueKey = $uniqueKey;
    }

    /**
     * Get uniqueKey.
     *
     * @return string
     */
    public function getUniqueKey(): string
    {
        return $this->uniqueKey;
    }
}
