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
     * @ORM\Column(name="Name", type="string", length=255, nullable=False)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="Email", type="string", length=255, nullable=False)
     */
    private $email;

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
     * Set name.
     *
     * @param string $name
     *
     * @return ParentAssignment
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set email.
     *
     * @param string $email
     *
     * @return ParentAssignment
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email.
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
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
