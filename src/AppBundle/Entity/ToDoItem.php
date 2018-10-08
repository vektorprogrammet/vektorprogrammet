<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * ToDoItem
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\ToDoItemRepository")
 */
class ToDoItem
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
     * @var \DateTime
     *
     * @ORM\Column(name="createdAt", type="date")
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="deletedAt", type="date", nullable=true)
     */
    private $deletedAt;

    /**
     * @var integer
     *
     * @ORM\Column(name="priority", type="integer")
     */
    private $priority;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=5000)
     */
    private $description;

    /**
     * @var Department
     *
     * @ORM\ManyToOne(targetEntity="Department")
     * @@ORM\JoinColumn(referencedColumnName="id", nullable=true)
     */
    private $department;


    /**
     * @var Semester
     *
     * @ORM\ManyToOne(targetEntity="Semester")
     * @ORM\JoinColumn(referencedColumnName="id", nullable=true)
     */
    //New, non-department-specific semester:
    private $semester;



    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="ToDoMandatory", mappedBy="toDoItem")
     */
    private $toDoMandatories;


    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="ToDoDeadline", mappedBy="toDoItem")
     */
    private $toDoDeadlines;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="ToDoCompleted", mappedBy="toDoItem")
     */
    private $toDoCompleted;


    public function __construct()
    {
        $this->toDoMandatories = new ArrayCollection();
        $this->toDoDeadlines = new ArrayCollection();
        $this->toDoCompleted = new ArrayCollection();
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
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return ToDoItem
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set deletedAt
     *
     * @param \DateTime $deletedAt
     * @return ToDoItem
     */
    public function setDeletedAt($deletedAt)
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }

    /**
     * Get deletedAt
     *
     * @return \DateTime
     */
    public function getDeletedAt(): ? \DateTime
    {
        return $this->deletedAt;
    }

    /**
     * Set priority
     *
     * @param integer $priority
     * @return ToDoItem
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;

        return $this;
    }

    /**
     * Get priority
     *
     * @return integer
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return ToDoItem
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return ToDoItem
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return Department
     */
    public function getDepartment(): ? Department
    {
        return $this->department;
    }

    /**
     * @param Department $department
     *
     */
    public function setDepartment(? Department $department)
    {
        $this->department = $department;
    }

    /**
     * @param Semester $semester
     */
    public function setSemester(? Semester $semester)
    {
        $this->semester = $semester;
    }

    /**
     * @return Semester
     */
    public function getSemester(): ? Semester
    {
        return $this->semester;
    }

    /**
     * @return ToDoMandatory[]
     */
    public function getToDoMandatories(): array
    {
        $sortedArray = $this->toDoMandatories->toArray();
        usort($sortedArray, function (ToDoMandatory $a, ToDoMandatory $b) {
            return ($a->getSemester()->getSemesterStartDate() < $b->getSemester()->getSemesterStartDate());
        });

        return $sortedArray;
    }

    /**
     * @param ToDoMandatory[] $toDoMandatories
     */
    public function setToDoMandatory(array $toDoMandatories): void
    {
        $this->toDoMandatories = $toDoMandatories;
    }

    /** Gets List of ToDoDeadline objects (with this semester), ordered by semesters start date
     * @return ToDoDeadline[]
     */
    public function getToDoDeadlines(): array
    {
        $sortedArray = $this->toDoDeadlines->toArray();
        usort($sortedArray, function (ToDoDeadline $a, ToDoDeadline $b) {
            return ($a->getSemester()->getSemesterStartDate() < $b->getSemester()->getSemesterStartDate());
        });
        return $sortedArray;
    }


    /**
     * @param ToDoDeadline[] $toDoDeadlines
     */
    public function setToDoDeadlines(array $toDoDeadlines): void
    {
        $this->toDoDeadlines = $toDoDeadlines;
    }



    /** Gets List of ToDoCompleted objects (with this semester), ordered by semesters start date
     * @return ToDoCompleted[]
     */
    public function getToDoCompleted(): array
    {
        $sortedArray = $this->toDoCompleted->toArray();
        usort($sortedArray, function (ToDoCompleted $a, ToDoCompleted $b) {
            return ($a->getSemester()->getSemesterStartDate() < $b->getSemester()->getSemesterStartDate());
        });
        return $sortedArray;
    }

    /**
     * @param ToDoCompleted[] $toDoCompleted
     */
    public function setToDoCompleted(array $toDoCompleted): void
    {
        $this->toDoCompleted = $toDoCompleted;
    }

    /**
     * @return bool
     */
    public function isMandatory()
    {
        if (empty($this->getToDoMandatories())) {
            return false;
        }
        $mandatories = $this->getToDoMandatories();
        return $mandatories[0]->isMandatory();
    }


    /**
     * @return bool
     */
    public function hasDeadlineThisSemester()
    {
        if (empty($this->getToDoDeadlines())) {
            return false;
        }
        $deadlines = $this->getToDoDeadlines();
        return ($deadlines[0]->getSemester()->getId() == $this->semester->getId());
    }

    /**
     * @return bool
     */
    public function isCompletedInSemesterByDepartment(Semester $semester, Department $department)
    {
        if (empty($this->getToDoCompleted())) {
            return false;
        }
        $completes = $this->getToDoCompleted();
        return (($completes[0]->getSemester()->getId() == $semester->getId())) and ($completes[0]->getDepartment()->getId() == $department->getId());
    }
}
