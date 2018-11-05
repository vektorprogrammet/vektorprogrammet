<?php

namespace AppBundle\Service;

use AppBundle\Entity\Department;
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

    public function itemHasDeadlineThisSemesterByDepartment(ToDoItem $item, Department $department)
    {
        if (empty($item->getToDoDeadlines())) {
            return false;
        } else {
            //TOT FIX:
            // currentsemester = em -> getCurrentSemester
            $currentsemester = $item->getSemester();
            $deadlines = $item->getToDoDeadlines();
            return ($deadlines[0]->getSemester()->getId() == $currentsemester->getId());
        }
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
     * @param ToDoItem $item
     * @param Semester $semester
     * @return bool
     */
    public function itemIsMandatoryBySemester(ToDoItem $item, Semester $semester){

            if (empty($item->getToDoMandatories())) {
                return false;
            }
            $mandatories = $item->getToDoMandatories();

    }

    /**
     * @param ToDoItem $a
     * @return bool
     */
    public function hasDeadLineShortly(ToDoItem $a) //department d
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
     * @param Semester $s
     * @return bool
     */
    public function filterMandatoryAndInsignificantDeadline(ToDoItem $a, Semester $s)
    {
        return ($a->isMandatoryBySemester($s) and !($this->hasDeadLineShortly($a)));
    }

    /**
     * @param array $toDoItems
     * @param Semester $semester
     * @return array
     */
    public function getMandatoryToDoItemsWithInsignificantDeadline(array $toDoItems, Semester $semester)
        {
        //$items = array_filter($toDoItems, array($this, "filterMandatoryAndInsignificantDeadline"));
        $items = array_filter($toDoItems, function (ToDoItem $a) use ($semester) {
            return ($a->isMandatoryBySemester($semester) and !($this->hasDeadLineShortly($a)));
        });
        return $items;
    }

    /* *
     * @param ToDoItem $a
     * @param Semester $s
     * @return bool
     * /
    public function filterNonMandatoryAndInsignificantDeadline(ToDoItem $a, Semester $s)
    {
        return !($a->isMandatoryBySemester($s) or ($this->hasDeadLineShortly($a)));
    } */

    /**
     * @param array $toDoItems
     * @param Semester $semester
     * @return array
     */
    public function getNonMandatoryToDoItemsWithInsignificantDeadline(array $toDoItems, Semester $semester)
    {
        //$items = array_filter($toDoItems,$semester, array($this, "filterNonMandatoryAndInsignificantDeadline"));
        $items = array_filter($toDoItems, function (ToDoItem $a) use ($semester) {
            return !($a->isMandatoryBySemester($semester) or ($this->hasDeadLineShortly($a)));
        });
        return $items;
    }

    /**
     * @param array $toDoItems
     * @param Semester $semester
     * @return array
     *
     */
    public function getIncompletedToDoItems(array $toDoItems, Semester $semester, Department $department)
    {
        $items = array_filter($toDoItems, function (ToDoItem $a) use ($semester, $department) {
            return !($a->isCompletedInSemesterByDepartment($semester,$department));
        });
        return $items;
    }

    public function getDeletedToDoItems(array $toDoItems)
    {
        $today = new \DateTime();
        $items = array_filter($toDoItems, function (ToDoItem $a) use ($today) {
            return !(($a->getDeletedAt() == null) or ($a->getDeletedAt() > $today));
        });
        return $items;
    }

    // Generate appropriate items from ToDoItemInfo, info from Type

    public function generateEntities(ToDoItemInfo $itemInfo)
    {

        $toDoItem = new ToDoItem();
        $department = $itemInfo->getDepartment();
        $toDoItem
            ->setCreatedAt(new \DateTime())
            ->setPriority($itemInfo->getPriority())
            ->setTitle($itemInfo->getTitle())
            ->setDescription($itemInfo->getDescription())
            ->setSemester($itemInfo->getSemester())
            ->setDepartment($department);

        $this->em->persist($toDoItem);

        if ($itemInfo->getIsMandatory()) {
            $toDoMandatory = new ToDoMandatory();
            $toDoMandatory
                ->setToDoItem($toDoItem)
                ->setIsMandatory(true)
                ->setSemester($itemInfo->getSemester());

            $this->em->persist($toDoMandatory);
        }

        $deadlineDate = $itemInfo->getDeadlineDate();
        if ($deadlineDate != null) {
            $toDoDeadLine = new ToDoDeadline();
            $toDoDeadLine
                ->setToDoItem($toDoItem)
                ->setSemester($itemInfo->getSemester())
                ->setDeadDate($deadlineDate);

            $this->em->persist($toDoDeadLine);
        }
        $this->em->flush();
    }


    public function editEntities(ToDoItemInfo $itemInfo, Semester $semester)
    {

        $toDoItem = $itemInfo->getToDoItem();
        $department = $itemInfo->getDepartment();
        $toDoItem
            ->setPriority($itemInfo->getPriority())
            ->setTitle($itemInfo->getTitle())
            ->setDescription($itemInfo->getDescription());

        $this->em->persist($toDoItem);

        // TOT DO: lag getCurrentSemester()
        //$currentSemester = $this->em->getRepository('AppBundle:Semester')->getCurrentSemester();
        //FOR NOW:
        $currentSemester = $itemInfo->getSemester();

        //TOT DO: hent neste semester:
        //$nextMandatory = this->getMandatoryBySemester($semester->getNextSemester());
        //FOR NOW:
        $nextMandatory = new ToDoMandatory();

        //hvis endre: du MÅ opprette en ny for neste semester og endre til det den var fra før

        //Hvis det finnes en mandatory entity for toDoen fra før

        $preExistingMandatory = $itemInfo->getToDoMandatory();
        $previousSetting = false;
        if (!empty($preExistingMandatory)) {
            //Hvis de er ulike
            if ($itemInfo->getIsMandatory() !== $itemInfo->getToDoMandatory()->isMandatory()) {
                $previousSetting = $preExistingMandatory->isMandatory();
                $preExistingMandatory->setIsMandatory(!$previousSetting);

                if (empty($preExistingMandatory->getSemester()) and (!empty($nextMandatory)) and ($semester !== $currentSemester) ){
                    $correctiveMandatory = new ToDoMandatory();
                    $correctiveMandatory
                        //->setSemester($semester->getNextSemester())
                        ->setIsMandatory($previousSetting)
                        ->setToDoItem($itemInfo->getToDoItem());
                }


                //Case: var mandatory, nå ikke
                // Endre den fra før sin isMandatory
                //hvis (semester == null) og (mandatory for neste semester == null) og dette ikke er current semester:
                //lag en mandatory for neste semester som er mandatory


                //Case: var ikke mandatory, er nå
                //endre den fra før sin isMandatory
                //hvis (semester == null) og (mandatory for neste semester == null) og dette ikke er current semester (som endres):
                //lag en mandatory for neste semester som er ikke mandatory

                //CASE: Var mandatory, er mandatory
                //do nothing
                //Case: var ikke mandatory, er ikke mandatory
                //do nothing

            }
        } else {

        }

        //Hvis det ikke finnes en mandatory entity for toDoen fra før
        //lag en ny mandatory og sett fra info
        //Hvis semester == null (gjelder flere semestre) og det ikke er current semester (som endres nå):
        //lag en mandatory for neste semester som er ikke mandatory



        $toDoMandatory = (empty($itemInfo->getToDoMandatory()) ? new ToDoItem() : $itemInfo->getToDoMandatory());





            if (($itemInfo->getIsMandatory() !== $itemInfo->getToDoMandatory()->isMandatory()) and (empty($nextMandatory))){
                $nextMandatory = new ToDoMandatory();
                $nextMandatory->setToDoItem($toDoItem)
                    ->setIsMandatory(!($itemInfo->getIsMandatory()))
                    //->setSemester($semester->getNextSemester());
                    ->setSemester($currentSemester);

            } else {
            if ($itemInfo->getIsMandatory()) {
                $toDoMandatory = new ToDoMandatory();
                $toDoMandatory
                    ->setToDoItem($toDoItem)
                    ->setIsMandatory(true)
                    ->setSemester($currentSemester);
                $this->em->persist($toDoMandatory);
            }
        }


        $deadlineDate = $itemInfo->getDeadlineDate();
        if ($deadlineDate != null) {
            $toDoDeadLine = new ToDoDeadline();
            $toDoDeadLine
                ->setToDoItem($toDoItem)
                ->setSemester($currentSemester)
                ->setDeadDate($deadlineDate);

            $this->em->persist($toDoDeadLine);
        }
        $this->em->flush();
    }

/*
    public function editEntities(ToDoItemInfo $itemInfo, ToDoItem $toDoItem, Department $department)
    {
        //$toDoItem = new ToDoItem();
        //$toDoItem->setCreatedAt(new \DateTime());
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
            if ($this->itemHasDeadlineThisSemesterByDepartment($toDoItem, $toDoItem->getDepartment())){
                //if (($itemInfo->getDepartment() == null) or ($itemInfo->getDepartment() === $toDoItem->getDepartment())){

                }
                $oldDeadlines = $toDoItem->getToDoDeadlines();
                foreach ($oldDeadlines as $deadline){
                    if ($deadline->getSemester() === $currentSemester){
                        $deadline->setDeadDate();
                    }
                }
            }
            $toDoDeadLine = new ToDoDeadline();
            $toDoDeadLine->setToDoItem($toDoItem);
            $toDoDeadLine->setSemester($currentSemester);
            $toDoDeadLine->setDeadDate($deadlineDate);

            $this->em->persist($toDoDeadLine);
        }
        $this->em->flush();
    }*/


    public function createToDoItemInfoFromItem(ToDoItem $item, Semester $semester){
        $infoItem = new ToDoItemInfo();
        $infoItem
            ->setSemester($item->getSemester())
            ->setDepartment($item->getDepartment())
            ->setPriority($item->getPriority())
            ->setTitle($item->getTitle())
            ->setDescription($item->getDescription())
            ->setIsMandatory($this->itemIsMandatoryBySemester($item, $semester))
            //->setDeadlineDate($item->getDeadlineDateBySemester($semester))
            ->setToDoItem($item);

        if ($infoItem->getIsMandatory()){
            $mandatoryItems = $item->getToDoMandatories();
            foreach ($mandatoryItems as $mandatory){
                if ($mandatory->getSemester() === $semester){
                    $infoItem->setToDoMandatory($mandatory);
                    break;
                }
            }
        }

        $toDoDeadline = $item->getDeadlineBySemester($semester);

        if (! empty($toDoDeadline)){
            $infoItem
                ->setToDoDeadline($toDoDeadline)
                ->setDeadlineDate($toDoDeadline->getDeadDate());
        }
        //Disse over skal eventuelt endres når brukeren endrer.
            //->setIsMandatory(new ToDoMandatory())
            //->setToDoDeadline(new ToDoDeadline());
    }

    public function completedItem(ToDoItem $item, Semester $semester, Department $department)
    {
        $completedItem = new ToDoCompleted();
        $completedItem
            ->setToDoItem($item)
            ->setSemester($semester)
            ->setCompletedAt(new \DateTime())
            ->setDepartment($department);

        $this->em->persist($completedItem);
        $this->em->flush();
    }

    public function deleteToDoItem(ToDoItem $item)
    {
        $item->setDeletedAt(new \DateTime());
        $this->em->persist($item);
        $this->em->flush();
    }

    public function toggleCompletedItem(ToDoItem $item, Semester $semester, Department $department)
    {
        $completedItem = $this->em->getRepository('AppBundle:ToDoCompleted')->findOneBy(array(
                'semester' => $semester,
                'department' => $department,
                'toDoItem' => $item,
            )
        );

        if ($completedItem != null) {
            $this->em->remove($completedItem);
            $this->em->flush();
            return true;
        } else {
            $completedItem = new ToDoCompleted();
            $completedItem
                ->setCompletedAt(new \DateTime())
                ->setSemester($semester)
                ->setDepartment($department)
                ->setToDoItem($item);
            $this->em->persist($completedItem);
            $this->em->flush();
            return true;
        }
    }




}
