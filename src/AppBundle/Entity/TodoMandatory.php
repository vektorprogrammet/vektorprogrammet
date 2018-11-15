<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TodoMandatory
 *
 * @ORM\Table(name="todo_mandatory")
 * @ORM\Entity
 */
class TodoMandatory
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
     * @ORM\ManyToOne(targetEntity="TodoItem", inversedBy="todoMandatories")
     */
    private $todoItem;

    /**
     * @var Semester
     *
     * @ORM\ManyToOne(targetEntity="Semester")
     */
    private $semester;



    /**
     * @var boolean
     *
     * @ORM\Column(name="isMandatory", type="boolean")
     */
    private $isMandatory;


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
     * Set isMandatory
     *
     * @param boolean $isMandatory
     * @return TodoMandatory
     */
    public function setIsMandatory($isMandatory)
    {
        $this->isMandatory = $isMandatory;

        return $this;
    }

    /**
     * Get isMandatory
     *
     * @return boolean
     */
    public function isMandatory()
    {
        return $this->isMandatory;
    }

    /**
     * @return TodoItem
     */
    public function getTodoItem(): TodoItem
    {
        return $this->todoItem;
    }

    /**
     * @param TodoItem $todoItem
     * @return $this
     */
    public function setTodoItem(TodoItem $todoItem)
    {
        $this->todoItem = $todoItem;
        return $this;
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
}
