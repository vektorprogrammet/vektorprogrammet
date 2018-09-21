<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ToDoDeadline
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class ToDoDeadline
{

    /**
     * @var ToDoItem
     *
     * @ORM\ManyToOne(targetEntity="ToDoItem", inversedBy="toDoDeadlines")
     */
    private $toDoItem;

    /**
     * @var Semester
     *
     * @ORM\OneToOne(targetEntity="Semester")
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
     * @var \DateTime
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
     * @param \DateTime $deadDate
     * @return ToDoDeadline
     */
    public function setDeadDate($deadDate)
    {
        $this->deadDate = $deadDate;

        return $this;
    }

    /**
     * Get deadDate
     *
     * @return \DateTime 
     */
    public function getDeadDate()
    {
        return $this->deadDate;
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
     * @param Semester $semester
     */
    public function setSemester(Semester $semester): void
    {
        $this->semester = $semester;
    }

    /**
     * @return Semester
     */
    public function getSemester(): Semester
    {
        return $this->semester;
    }

}
