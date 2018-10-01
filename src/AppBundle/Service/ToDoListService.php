<?php

namespace AppBundle\Service;

use AppBundle\Entity\Repository\SemesterRepository;
use AppBundle\Entity\Semester;
use Doctrine\ORM\EntityManager;
use AppBundle\Entity\ToDoItem;
use AppBundle\Entity\ToDoMandatory;
use AppBundle\Entity\ToDoDeadline;
use AppBundle\Entity\ToDoCompleted;
use AppBundle\Model\ToDoItemInfo;

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
    public function getMandatoryToDoItems(array $toDoItems)
    {
        $mandatoryItems = array_filter($toDoItems, function (ToDoItem $a) {
            return ($a->isMandatory());
        });

        return $mandatoryItems;
    }

    /**
     * @param ToDoItem $a
     * @return bool
     */
    public function hasDeadLineShortly(ToDoItem $a)
    {
        if ($a->hasDeadlineThisSemester()) {
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
    public function filterMandatoryAndInsignificantDeadline(ToDoItem $a)
    {
        return ($a->isMandatory() and !($this->hasDeadLineShortly($a)));
    }

    /**
     * @param array $toDoItems
     * @return array
     */
    public function getMandatoryToDoItemsWithInsignificantDeadline(array $toDoItems)
    {
        $items = array_filter($toDoItems, array($this, "filterMandatoryAndInsignificantDeadline"));
        return $items;
    }

    /**
     * @param ToDoItem $a
     * @return bool
     */
    public function filterNonMandatoryAndInsignificantDeadline(ToDoItem $a)
    {
        return !($a->isMandatory() or ($this->hasDeadLineShortly($a)));
    }

    /**
     * @param array $toDoItems
     * @return array
     */
    public function getNonMandatoryToDoItemsWithInsignificantDeadline(array $toDoItems)
    {
        $items = array_filter($toDoItems, array($this, "filterNonMandatoryAndInsignificantDeadline"));
        return $items;
    }

    /**
     * @param array $toDoItems
     * @param Semester $semester
     * @return array
     *
     */
    public function getIncompletedToDoItems(array $toDoItems, Semester $semester)
    {
        $items = array_filter($toDoItems, function (ToDoItem $a) use ($semester) {
            return !($a->isCompletedInSemester($semester));
        });
        return $items;
    }

    public function generateEntities(ToDoItemInfo $itemInfo){
        $entities = array();
        $entities->append($itemInfo->getToDoItem());
        $entities->append($itemInfo->getToDoMandatory());
        $deadline = $itemInfo->getToDoDeadline();
        $this->em->persist($item);
        $this->em->flush();
    }

    public function generateEntities(ToDoItemInfo $itemInfo){
        $toDoItem = new ToDoItem();
        $toDoItem->setCreatedAt(new \DateTime());
        $toDoItem->setPriority($itemInfo->getPriority());
        $toDoItem->setTitle($itemInfo->getTitle());
        $toDoItem->setDescription($itemInfo->getDescription());
        $semester = $itemInfo->getSemester();
        if ($semester != null){
            $toDoItem->setSemester($semester);
        }

        $department = $itemInfo->getDepartment();
        if ($department != null){
            $toDoItem->setDepartment($department);
        }

        if ($itemInfo->getIsMandatory()){
            $toDoMandatory = new ToDoMandatory();
            $toDoMandatory->setToDoItem($toDoItem);
            $toDoMandatory->setIsMandatory(true);
            $toDoMandatory->setSemester($semester);
            //$toDoItem->setToDoMandatory($toDoMandatory);
            $this->toDoMandatory = $toDoMandatory;
        }
        $deadlineDate = $itemInfo->getToDoDeadline();
        if ($deadlineDate != null){
            $toDoDeadLine = new ToDoDeadline();
            $toDoDeadLine->setToDoItem($toDoItem);
            $toDoDeadLine->setSemester($semester);
            $toDoDeadLine->setDeadDate($deadlineDate);
            $this->toDoDeadline = $toDoDeadLine;
        }
        $currentSemester = $this->em->getRepository('AppBundle:Semester')->


        $this->toDoItem = $toDoItem;

    }


}
