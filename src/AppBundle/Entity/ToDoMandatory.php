<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ToDoMandatory
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class ToDoMandatory
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
     * @ORM\ManyToOne(targetEntity="ToDoItem", inversedBy="toDoMandatories")
     */
    private $toDoItem;

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

    /* *
     * @var \DateTime
     *
     * @ORM\Column(name='dateDecided', type="datetime")
     * /
    private $dateDecided;
     */

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
     * @return ToDoMandatory
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
     * @return ToDoItem
     */
    public function getToDoItem(): ToDoItem
    {
        return $this->toDoItem;
    }

    /**
     * @param ToDoItem $toDoItem
     * @return $this
     */
    public function setToDoItem(ToDoItem $toDoItem)
    {
        $this->toDoItem = $toDoItem;
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
    /*
        /**
         * @return \DateTime
         * /
        public function getDateDecided(): \DateTime
        {
            return $this->dateDecided;
        }

        /**
         * @param \DateTime $dateDecided
         * /
        public function setDateDecided(\DateTime $dateDecided): void
        {
            $this->dateDecided = $dateDecided;
        }*/
}
