<?php

namespace AppBundle\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * TodoItem
 *
 * @ORM\Table(name="todo_item")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\TodoItemRepository")
 */
class TodoItem
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
     * @var DateTime
     *
     * @ORM\Column(name="createdAt", type="date")
     */
    private $createdAt;

    /**
     * @var DateTime
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
    private $semester;



    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="TodoMandatory", mappedBy="todoItem")
     */
    private $todoMandatories;


    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="TodoDeadline", mappedBy="todoItem")
     */
    private $todoDeadlines;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="TodoCompleted", mappedBy="todoItem")
     */
    private $todoCompleted;


    /**
     * TodoItem constructor.
     */
    public function __construct()
    {
        $this->todoMandatories = new ArrayCollection();
        $this->todoDeadlines = new ArrayCollection();
        $this->todoCompleted = new ArrayCollection();
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
     * @param DateTime $createdAt
     * @return TodoItem
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set deletedAt
     *
     * @param DateTime $deletedAt
     * @return TodoItem
     */
    public function setDeletedAt($deletedAt)
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }

    /**
     * Get deletedAt
     *
     * @return DateTime
     */
    public function getDeletedAt(): ? DateTime
    {
        return $this->deletedAt;
    }

    /**
     * Set priority
     *
     * @param integer $priority
     * @return TodoItem
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
     * @return TodoItem
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
     * @return TodoItem
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
     * @param \AppBundle\Entity\Semester|null $semester
     * @return $this
     */
    public function setSemester(? Semester $semester)
    {
        $this->semester = $semester;
        return $this;
    }

    /**
     * @return Semester
     */
    public function getSemester(): ? Semester
    {
        return $this->semester;
    }

    /**
     * @return TodoMandatory[]
     */
    public function getTodoMandatories(): array
    {
        $sortedArray = $this->todoMandatories->toArray();
        usort($sortedArray, function (TodoMandatory $a, TodoMandatory $b) {
            return ($a->getSemester()->getSemesterStartDate() < $b->getSemester()->getSemesterStartDate());
        });

        return $sortedArray;
    }

    /**
     * @param TodoMandatory[] $todoMandatories
     * @return $this
     */
    public function setTodoMandatory(array $todoMandatories)//: void
    {
        $this->todoMandatories = $todoMandatories;
        return $this;
    }

    /** Gets List of TodoDeadline objects (with this semester), ordered by semesters start date
     * @return TodoDeadline[]
     */
    public function getTodoDeadlines(): array
    {
        $sortedArray = $this->todoDeadlines->toArray();
        usort($sortedArray, function (TodoDeadline $a, TodoDeadline $b) {
            return ($a->getSemester()->getSemesterStartDate() < $b->getSemester()->getSemesterStartDate());
        });
        return $sortedArray;
    }


    /**
     * @param TodoDeadline[] $todoDeadlines
     * @return $this
     */
    public function setTodoDeadlines(array $todoDeadlines)//: void
    {
        $this->todoDeadlines = $todoDeadlines;
        return $this;
    }



    /** Gets List of TodoCompleted objects (with this semester), ordered by semesters start date
     * @return TodoCompleted[]
     */
    public function getTodoCompleted(): array
    {
        $sortedArray = $this->todoCompleted->toArray();
        usort($sortedArray, function (TodoCompleted $a, TodoCompleted $b) {
            return ($a->getSemester()->getSemesterStartDate() < $b->getSemester()->getSemesterStartDate());
        });
        return $sortedArray;
    }

    /**
     * @param array $todoCompleted
     * @return $this
     */
    public function setTodoCompleted(array $todoCompleted) //: void
    {
        $this->todoCompleted = $todoCompleted;
        return $this;
    }


    /**
     * @param \AppBundle\Entity\Semester $semester
     * @return bool
     */
    public function isMandatoryBySemester(Semester $semester)
    {
        $mandatory = $this->getMandatoryBySemester($semester);
        if (!empty($mandatory)) {
            return $mandatory->isMandatory();
        }
        $mandatories = $this->getTodoMandatories();
        if (empty($mandatories)) {
            return false;
        }
        foreach ($mandatories as $mandatory) {
            if ($mandatory->getSemester()->isBefore($semester)) {
                return $mandatory->isMandatory();
            }
        }
        return false;
    }

    /**
     * @param \AppBundle\Entity\Semester $semester
     * @return TodoMandatory|null
     */
    public function getMandatoryBySemester(Semester $semester)
    {
        $mandatories = $this->getTodoMandatories();
        foreach ($mandatories as $element) {
            if (($element->getSemester() === $semester)) {
                return $element;
            }
        }
        return null;
    }


    /**
     * @param \AppBundle\Entity\Semester $semester
     * @return TodoDeadline|null
     */
    public function getDeadlineBySemester(Semester $semester)
    {
        if (empty($this->getTodoDeadlines())) {
            return null;
        }
        $deadlines = $this->getTodoDeadlines();
        foreach ($deadlines as $deadline) {
            if ($deadline->getSemester() === $semester) {
                return $deadline;
            }
        }
        return null;
    }


    /**
     * @param \AppBundle\Entity\Semester $semester
     * @return bool
     */
    public function hasDeadlineBySemester(Semester $semester)
    {
        $deadline = $this->getDeadlineBySemester($semester);

        return $deadline !== null;
    }

    /**
     * @param \AppBundle\Entity\Semester $semester
     * @return bool
     */
    public function isPastDeadlineBySemester(Semester $semester)
    {
        $deadline = $this->getDeadlineBySemester($semester);

        return $deadline === null ? false: $deadline->getDeadDate() < new DateTime();
    }


    /**
     * @param Semester $semester
     * @return bool
     */
    public function hasShortDeadlineBySemester(Semester $semester)
    {
        $deadline = $this->getDeadlineBySemester($semester);
        if ($deadline === null) {
            return false;
        }
        return ($deadline->getDeadDate() <= new DateTime('+2 weeks'));
    }


    /**
     * @param \AppBundle\Entity\Semester $semester
     * @param Department $department
     * @return bool
     */
    public function isCompletedInSemesterByDepartment(Semester $semester, Department $department)
    {
        $completes = $this->getTodoCompleted();
        if ($completes === null) {
            return false;
        }
        foreach ($completes as $completed) {
            if (($completed->getSemester() === $semester) && ($completed->getDepartment() === $department)) {
                return true;
            }
        }
        return false;
    }
}
