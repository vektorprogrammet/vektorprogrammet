<?php

namespace AppBundle\Model;

use AppBundle\Entity\Department;
use AppBundle\Entity\Semester;
use AppBundle\Entity\TodoDeadline;
use AppBundle\Entity\TodoItem;
use AppBundle\Entity\TodoMandatory;
use DateTime;

class TodoItemInfo
{
    private $semester;
    private $department;
    private $priority;
    private $title;
    private $description;

    private $isMandatory;
    private $deadlineDate;

    private $todoItem;
    private $todoMandatory;
    private $todoDeadline;

    public function __construct()
    {
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
     * @param Semester $semester
     * @return TodoItemInfo
     */
    public function setSemester(? Semester $semester): ? TodoItemInfo
    {
        $this->semester = $semester;
        return $this;
    }

    /**
     * @return Department
     */
    public function getDepartment(): ?Department
    {
        return $this->department;
    }

    /**
     * @param Department $department
     * @return TodoItemInfo
     */
    public function setDepartment(? Department $department): ? TodoItemInfo
    {
        $this->department = $department;
        return $this;
    }

    /**
     * @return int
     */
    public function getPriority() : ?int
    {
        return $this->priority;
    }

    /**
     * @param int $priority
     * @return TodoItemInfo
     */
    public function setPriority(int $priority): TodoItemInfo
    {
        $this->priority = $priority;
        return $this;
    }

    /**
     * @return string
     */
    public function getTitle(): ? string
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return TodoItemInfo
     */
    public function setTitle(string $title): TodoItemInfo
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescription(): ? string
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return TodoItemInfo
     */
    public function setDescription(string $description): TodoItemInfo
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getDeadlineDate(): ?DateTime
    {
        return $this->deadlineDate;
    }

    /**
     * @param DateTime $deadlineDate
     * @return TodoItemInfo
     */
    public function setDeadlineDate(DateTime $deadlineDate = null): TodoItemInfo
    {
        $this->deadlineDate = $deadlineDate;
        return $this;
    }

    /**
     * @return TodoItem
     */
    public function getTodoItem() : TodoItem
    {
        return $this->todoItem;
    }

    /**
     * @param TodoItem $todoItem
     * @return TodoItemInfo
     */
    public function setTodoItem($todoItem)
    {
        $this->todoItem = $todoItem;
        return $this;
    }

    /**
     * @return TodoMandatory|null
     */
    public function getTodoMandatory() : ? TodoMandatory
    {
        return $this->todoMandatory;
    }

    /**
     * @param TodoMandatory $todoMandatory
     * @return TodoItemInfo
     */
    public function setTodoMandatory($todoMandatory)
    {
        $this->todoMandatory = $todoMandatory;
        return $this;
    }

    /**
     * @return TodoDeadline
     */
    public function getTodoDeadline()
    {
        return $this->todoDeadline;
    }

    /**
     * @param TodoDeadline $todoDeadline
     * @return TodoItemInfo
     */
    public function setTodoDeadline($todoDeadline)
    {
        $this->todoDeadline = $todoDeadline;
        return $this;
    }

    /**
     * @param bool $isMandatory
     * @return $this
     */
    public function setIsMandatory($isMandatory)
    {
        $this->isMandatory = $isMandatory;
        return $this;
    }

    /**
     * @return bool
     */
    public function getIsMandatory()
    {
        return $this->isMandatory;
    }
}
