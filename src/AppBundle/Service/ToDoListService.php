<?php

namespace AppBundle\Service;


use AppBundle\Entity\Semester;
use Doctrine\ORM\EntityManager;
use AppBundle\Entity\ToDoItem;




class ToDoListService
{
    /**
     * @var EntityManager
     *
     */
    private $em;


    /**
     * ToDoListService constructor.
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;

    }

    /**
     * @param ToDoItem[] $toDoItems
     * @return array
     */
    public function getMandatoryToDoItems(array $toDoItems){

        $mandatoryItems = array_filter($toDoItems, function (ToDoItem $a ){
            return ($a->isMandatory());
        });

        return $mandatoryItems;
    }

    /**
     * @param ToDoItem $a
     * @return bool
     */
    function hasDeadLineShortly(ToDoItem $a){
            if ($a->hasDeadlineThisSemester()){
                $deadline = $a->getToDoDeadlines()[0]->getDeadDate();
                $now = new \DateTime();
                return ($deadline < $now->modify("+2 weeks"));
            } else {
                return false;
            }
    }

    /**
     * @param array $todoItems
     * @return array
     */
    public function getToDoItemsWithShortDeadline(array $todoItems)
    {

        $items = array_filter($todoItems, array($this, "hasDeadLineShortly"));

        return $items;
    }

    /**
     * @param ToDoItem $a
     * @return bool
     */
    function filterMandatoryAndInsignificantDeadline(ToDoItem $a){
        return ($a->isMandatory() and !($this->hasDeadLineShortly($a)));
    }

    /**
     * @param array $toDoItems
     * @return array
     */
    public function getMandatoryToDoItemsWithInsignificantDeadline(array $toDoItems){
        $items = array_filter($toDoItems, array($this, "filterMandatoryAndInsignificantDeadline"));
        return $items;
    }

    /**
     * @param ToDoItem $a
     * @return bool
     */
    function filterNonMandatoryAndInsignificantDeadline(ToDoItem $a){
            return !($a->isMandatory() or ($this->hasDeadLineShortly($a)));
    }

    /**
     * @param array $toDoItems
     * @return array
     */
    public function getNonMandatoryToDoItemsWithInsignificantDeadline(array $toDoItems){
        $items = array_filter($toDoItems, array($this, "filterNonMandatoryAndInsignificantDeadline"));
        return $items;
    }

    /**
     * @param array $toDoItems
     * @param Semester $semester
     * @return array
     *
     */
    public function getIncompletedToDoItems(array $toDoItems, Semester $semester){
        $items = array_filter($toDoItems, function (ToDoItem $a)use($semester){
            return !($a->isCompletedInSemester($semester));
        });
        return $items;
    }



}