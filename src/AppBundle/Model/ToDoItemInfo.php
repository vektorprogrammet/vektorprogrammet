<?php

namespace AppBundle\Model;

use AppBundle\Entity\Department;
use AppBundle\Entity\Semester;

class ToDoItemInfo
{
    private $semester;
    private $department;
    private $priority;
    private $title;
    private $description;

    private $isMandatory;
    private $deadlineDate;

    private $toDoItem;
    private $toDoMandatory;
    private $toDoDeadline;

    public function __construct()
    {
        return $this;
    }


    /**
     * @return Semester
     */
    public function getSemester(): ?Semester
    {
        return $this->semester;
    }

    /**
     * @param Semester $semester
     * @return ToDoItemInfo
     */
    public function setSemester(Semester $semester): ToDoItemInfo
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
     * @return ToDoItemInfo
     */
    public function setDepartment(Department $department): ToDoItemInfo
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
     * @return ToDoItemInfo
     */
    public function setPriority(int $priority): ToDoItemInfo
    {
        $this->priority = $priority;
        return $this;
    }

    /**
     * @return string
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return ToDoItemInfo
     */
    public function setTitle(string $title): ToDoItemInfo
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return ToDoItemInfo
     */
    public function setDescription(string $description): ToDoItemInfo
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDeadlineDate(): ?\DateTime
    {
        return $this->deadlineDate;
    }

    /**
     * @param \DateTime $deadlineDate
     * @return ToDoItemInfo
     */
    public function setDeadlineDate(\DateTime $deadlineDate = null): ToDoItemInfo
    {
        $this->deadlineDate = $deadlineDate;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getToDoItem()
    {
        return $this->toDoItem;
    }

    /**
     * @param mixed $toDoItem
     * @return ToDoItemInfo
     */
    public function setToDoItem($toDoItem)
    {
        $this->toDoItem = $toDoItem;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getToDoMandatory()
    {
        return $this->toDoMandatory;
    }

    /**
     * @param mixed $toDoMandatory
     * @return ToDoItemInfo
     */
    public function setToDoMandatory($toDoMandatory)
    {
        $this->toDoMandatory = $toDoMandatory;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getToDoDeadline()
    {
        return $this->toDoDeadline;
    }

    /**
     * @param mixed $toDoDeadline
     * @return ToDoItemInfo
     */
    public function setToDoDeadline($toDoDeadline)
    {
        $this->toDoDeadline = $toDoDeadline;
        return $this;
    }

    /**
     * @param $isMandatory
     * @return $this
     */
    public function setIsMandatory($isMandatory)
    {
        $this->isMandatory = $isMandatory;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getIsMandatory()
    {
        return $this->isMandatory;
    }

}
