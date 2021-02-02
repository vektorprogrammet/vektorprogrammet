<?php

namespace AppBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * TodoCompleted
 *
 * @ORM\Table(name="todo_completed")
 * @ORM\Entity
 */
class TodoCompleted
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
     * @var TodoItem
     *
     * @ORM\ManyToOne(targetEntity="TodoItem", inversedBy="todoCompleted")
     */
    private $todoItem;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="completedAt", type="date")
     */
    private $completedAt;

    /**
     * @var Semester
     *
     * @ORM\ManyToOne(targetEntity="Semester")
     */
    private $semester;

    /**
     * @var Department
     *
     * @ORM\ManyToOne(targetEntity="Department")
     */
    private $department;


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
     * Set completedAt
     *
     * @param DateTime $completedAt
     * @return TodoCompleted
     */
    public function setCompletedAt($completedAt)
    {
        $this->completedAt = $completedAt;
        return $this;
    }

    /**
     * Get completedAt
     *
     * @return DateTime
     */
    public function getCompletedAt()
    {
        return $this->completedAt;
    }

    /**
     * @return Semester
     */
    public function getSemester(): Semester
    {
        return $this->semester;
    }

    /**
     * @param Semester $semester
     * @return $this
     */
    public function setSemester(Semester $semester)//: void
    {
        $this->semester = $semester;
        return $this;
    }

    /**
     * @param TodoItem $todoItem
     * @return $this
     */
    public function setTodoItem(TodoItem $todoItem)//: void
    {
        $this->todoItem = $todoItem;
        return $this;
    }

    /**
     * @return TodoItem
     */
    public function getTodoItem(): TodoItem
    {
        return $this->todoItem;
    }

    /**
     * @param Department $department
     * @return $this
     */
    public function setDepartment(Department $department)//: void
    {
        $this->department = $department;
        return $this;
    }

    /**
     * @return Department
     */
    public function getDepartment(): Department
    {
        return $this->department;
    }
}
