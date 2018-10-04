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

    // Generate appropriate items from ToDoItemInfo, info from Type
    public function generateEntities(ToDoItemInfo $itemInfo)
    {
        $toDoItem = new ToDoItem();
        $toDoItem->setCreatedAt(new \DateTime());
        $toDoItem->setPriority($itemInfo->getPriority());
        $toDoItem->setTitle($itemInfo->getTitle());
        $toDoItem->setDescription($itemInfo->getDescription());
        $toDoItem->setSemester($itemInfo->getSemester());
        $department = $itemInfo->getDepartment();
        $toDoItem->setDepartment($department);

        $this->em->persist($toDoItem);

        // TOT DO: lag getCurrentSemester()
        //$currentSemester = $this->em->getRepository('AppBundle:Semester')->getCurrentSemester();
        //FOR NOW:
        $currentSemester = $itemInfo->getSemester();

        if ($itemInfo->getIsMandatory()) {
            $toDoMandatory = new ToDoMandatory();
            $toDoMandatory->setToDoItem($toDoItem);
            $toDoMandatory->setIsMandatory(true);
            $toDoMandatory->setSemester($currentSemester);

            $this->em->persist($toDoMandatory);
        }
        $deadlineDate = $itemInfo->getDeadlineDate();
        if ($deadlineDate != null) {
            $toDoDeadLine = new ToDoDeadline();
            $toDoDeadLine->setToDoItem($toDoItem);
            $toDoDeadLine->setSemester($currentSemester);
            $toDoDeadLine->setDeadDate($deadlineDate);

            $this->em->persist($toDoDeadLine);
        }
        $this->em->flush();
    }
}
