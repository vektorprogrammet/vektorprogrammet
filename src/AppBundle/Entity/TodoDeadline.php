<?php

namespace AppBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * TodoDeadline
 *
 * @ORM\Table(name="todo_deadline")
 * @ORM\Entity
 */
class TodoDeadline
{

    /**
     * @var TodoItem
     *
     * @ORM\ManyToOne(targetEntity="TodoItem", inversedBy="todoDeadlines")
     */
    private $todoItem;

    /**
     * @var Semester
     *
     * @ORM\ManyToOne(targetEntity="Semester")
     */
    private $semester;


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
     * @ORM\Column(name="deadDate", type="date")
     */
    private $deadDate;


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
     * Set deadDate
     *
     * @param DateTime $deadDate
     * @return TodoDeadline
     */
    public function setDeadDate($deadDate)
    {
        $this->deadDate = $deadDate;

        return $this;
    }

    /**
     * Get deadDate
     *
     * @return DateTime
     */
    public function getDeadDate()
    {
        return $this->deadDate;
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
     * @param Semester $semester
     * @return $this
     */
    public function setSemester(Semester $semester)//: void
    {
        $this->semester = $semester;
        return $this;
    }

    /**
     * @return Semester
     */
    public function getSemester(): Semester
    {
        return $this->semester;
    }
}
