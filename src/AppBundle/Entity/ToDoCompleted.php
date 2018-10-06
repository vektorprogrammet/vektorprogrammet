<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ToDoCompleted
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class ToDoCompleted
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
     * @var ToDoItem
     *
     * @ORM\ManyToOne(targetEntity="ToDoItem", inversedBy="toDoCompleted")
     */
    private $toDoItem;

    /**
     * @var \DateTime
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
     * @param \DateTime $completedAt
     * @return ToDoCompleted
     */
    public function setCompletedAt($completedAt)
    {
        $this->completedAt = $completedAt;

        return $this;
    }

    /**
     * Get completedAt
     *
     * @return \DateTime
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
     */
    public function setSemester(Semester $semester): void
    {
        $this->semester = $semester;
    }

    /**
     * @param ToDoItem $toDoItem
     */
    public function setToDoItem(ToDoItem $toDoItem): void
    {
        $this->toDoItem = $toDoItem;
    }

    /**
     * @return ToDoItem
     */
    public function getToDoItem(): ToDoItem
    {
        return $this->toDoItem;
    }

    /**
     * @param Department $department
     */
    public function setDepartment(Department $department): void
    {
        $this->department = $department;
    }

    /**
     * @return Department
     */
    public function getDepartment(): Department
    {
        return $this->department;
    }
}
