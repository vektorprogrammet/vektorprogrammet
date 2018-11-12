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


    /**
     * @param ToDoItem $item
     * @param Department $department
     * @return bool
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function itemHasDeadlineThisSemesterByDepartment(ToDoItem $item, Department $department)
    {
        if (empty($item->getToDoDeadlines())) {
            return false;
        } else {
            $currentSemester = $this->em->getRepository('AppBundle:Semester')->findCurrentSemester();
            $deadlines = $item->getToDoDeadlines();
            return ($deadlines[0]->getSemester()->getId() == $currentSemester->getId());
        }
    }

    /**
     * @param ToDoItem[] $todoItems
     * @return array
     */
    public function sortByPriority(array $todoItems)
    {
        $sortedArray = $todoItems;
        usort($sortedArray, function (ToDoItem $a, ToDoItem $b) {
            return ($a->getPriority() < $b->getPriority());
        });
        return $sortedArray;
    }

    /**
     * @param ToDoItem[] $todoItems
     * @param $semester
     * @return array
     */
    public function getMandatoryToDoItems(array $todoItems, $semester)
    {
        $mandatoryItems = array_filter($todoItems, function (ToDoItem $a) use ($semester) {
            return ($a->isMandatoryBySemester($semester));
        });

        return $mandatoryItems;
    }


    /**
     * @param ToDoItem $item
     * @param Semester $semester
     * @return ToDoMandatory|null
     */
    public function getMandatoryBySemester(ToDoItem $item, Semester $semester) : ? ToDoMandatory
    {
        if (empty($item->getToDoMandatories())) {
            return null;
        }
        $mandatories = $item->getToDoMandatories();
        foreach ($mandatories as $mandatory) {
            if ($mandatory->getSemester() === $semester) {
                return $mandatory;
            }
        }
        return null;
    }

    /**
     * @param ToDoItem $a
     * @return bool
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function hasDeadLineShortly(ToDoItem $a) //department d
    {
        return ($a->hasShortDeadlineBySemester($this->em->getRepository('AppBundle:Semester')->findCurrentSemester()));
    }

    /**
     * @param array $todoItems
     * @return ToDoItem[]
     */
    public function getToDoItemsWithShortDeadline(array $todoItems)
    {
        $items = array_filter($todoItems, array($this, "hasDeadLineShortly"));
        usort($items, function (ToDoItem $a, ToDoItem $b) {
            return ($a->getToDoDeadlines()[0]->getDeadDate() > $b->getToDoDeadlines()[0]->getDeadDate());
        });
        return $items;
    }

    /**
     * @param ToDoItem $a
     * @param Semester $s
     * @return bool
     * @throws \Doctrine\ORM\NonUniqueResultException
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
        $items = array_filter($toDoItems, function (ToDoItem $a) use ($semester) {
            return ($a->isMandatoryBySemester($semester) and !($this->hasDeadLineShortly($a)));
        });
        return $this->sortByPriority($items);
    }

    /**
     * @param array $toDoItems
     * @param Semester $semester
     * @return array
     */
    public function getNonMandatoryToDoItemsWithInsignificantDeadline(array $toDoItems, Semester $semester)
    {
        $items = array_filter($toDoItems, function (ToDoItem $a) use ($semester) {
            return !($a->isMandatoryBySemester($semester) or ($this->hasDeadLineShortly($a)));
        });
        return $this->sortByPriority($items);
    }

    /**
     * @param array $toDoItems
     * @param Semester $semester
     * @param Department $department
     * @return array
     */
    public function getIncompletedToDoItems(array $toDoItems, Semester $semester, Department $department)
    {
        $items = array_filter($toDoItems, function (ToDoItem $a) use ($semester, $department) {
            return !($a->isCompletedInSemesterByDepartment($semester, $department));
        });
        return array_values($items);
    }

    public function getDeletedToDoItems(array $toDoItems)
    {
        $today = new \DateTime();
        $items = array_filter($toDoItems, function (ToDoItem $a) use ($today) {
            return !(($a->getDeletedAt() == null) or ($a->getDeletedAt() > $today));
        });
        return array_values($items);
    }

    // Generate appropriate items from ToDoItemInfo, info from Type

    /**
     * @param ToDoItemInfo $itemInfo
     * @param EntityManager $em
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function generateEntities(ToDoItemInfo $itemInfo, EntityManager $em)
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

        $correctSemester = empty($itemInfo->getSemester()) ?
            $this->em->getRepository('AppBundle:Semester')->findCurrentSemester() :
            $itemInfo->getSemester();

        if ($itemInfo->getIsMandatory()) {
            $toDoMandatory = new ToDoMandatory();
            $toDoMandatory
                ->setToDoItem($toDoItem)
                ->setIsMandatory(true)
                ->setSemester($correctSemester);
            $this->em->persist($toDoMandatory);
        }

        $deadlineDate = $itemInfo->getDeadlineDate();
        if ($deadlineDate != null) {
            $toDoDeadLine = new ToDoDeadline();
            $toDoDeadLine
                ->setToDoItem($toDoItem)
                ->setSemester($correctSemester)
                ->setDeadDate($deadlineDate);

            $this->em->persist($toDoDeadLine);
        }
        $this->em->flush();
    }


    /**
     * @param ToDoItemInfo $itemInfo
     * @param Semester $semester
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function editEntities(ToDoItemInfo $itemInfo, Semester $semester)
    {
        $toDoItem = $itemInfo->getToDoItem();
        $toDoItem
            ->setPriority($itemInfo->getPriority())
            ->setTitle($itemInfo->getTitle())
            ->setDescription($itemInfo->getDescription())
            ->setSemester($itemInfo->getSemester())
            ->setDepartment($itemInfo->getDepartment());

        $this->em->persist($toDoItem);

        $preExistingMandatory = $itemInfo->getToDoMandatory();
        $previousMandatoryStatus = empty($preExistingMandatory) ? false : $preExistingMandatory->isMandatory();
        $sr  = $this->em->getRepository('AppBundle:Semester');
        $currentSemester = $sr->findCurrentSemester();


        if ($itemInfo->getIsMandatory() !== $previousMandatoryStatus) {
            if (empty($preExistingMandatory)) {
                $currentToDoMandatory = new ToDoMandatory();
                $currentToDoMandatory
                    ->setIsMandatory($itemInfo->getIsMandatory())
                    ->setSemester($semester)
                    ->setToDoItem($toDoItem);
                $this->em->persist($currentToDoMandatory);
            } else {
                $preExistingMandatory->setIsMandatory($itemInfo->getIsMandatory());
                $this->em->persist($preExistingMandatory);
            }

            if ($semester !== $currentSemester) {
                $nextSemester = $sr->getNextActive($semester);
                $nextMandatory = $this->getMandatoryBySemester($toDoItem, $nextSemester);
                if (empty($nextMandatory)) {
                    $newNextMandatory = new ToDoMandatory();
                    $newNextMandatory
                        ->setIsMandatory(!($itemInfo->getIsMandatory()))
                        ->setSemester($nextSemester)
                        ->setToDoItem($toDoItem);
                    $this->em->persist($newNextMandatory);
                } //Else null problem: den neste mandatorien setter ting straight
            }
        }
        /*
                //hvis endre: du MÅ opprette en ny for neste semester og endre til det den var fra før

                //Hvis det finnes en mandatory entity for toDoen fra før


                //Hvis de er ulike:
                if (!empty($preExistingMandatory)) {
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
        */

        $deadlineDate = $itemInfo->getDeadlineDate();
        $previousDeadLine = $toDoItem->getDeadlineBySemester($semester);
        if ($deadlineDate != null) {
            if (empty($previousDeadLine)) {
                $toDoDeadLine = new ToDoDeadline();
                $toDoDeadLine
                    ->setToDoItem($toDoItem)
                    ->setSemester($currentSemester)
                    ->setDeadDate($deadlineDate);

                $this->em->persist($toDoDeadLine);
            } else {
                $previousDeadLine->setDeadDate($itemInfo->getDeadlineDate());
                $this->em->persist($previousDeadLine);
            }
            //hvis noen har fjernet datoen i endre:
        } elseif (!empty($previousDeadLine)) {
            $this->em->remove($previousDeadLine);
        }
        $this->em->flush();
    }

    /*   OLD CODE:
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


    /**
     * @param ToDoItem $item
     * @param Semester $semester
     * @return ToDoItemInfo
     */
    public function createToDoItemInfoFromItem(ToDoItem $item, Semester $semester)
    {
        $infoItem = new ToDoItemInfo();
        $infoItem
            ->setSemester($item->getSemester())
            ->setDepartment($item->getDepartment())
            ->setPriority($item->getPriority())
            ->setTitle($item->getTitle())
            ->setDescription($item->getDescription())
            ->setToDoItem($item);

        $mandatory = $this->getMandatoryBySemester($item, $semester);
        $infoItem->setIsMandatory(empty($mandatory) ? false : $mandatory->isMandatory());

        if ($infoItem->getIsMandatory()) {
            $mandatoryItems = $item->getToDoMandatories();
            foreach ($mandatoryItems as $mandatory) {
                if ($mandatory->getSemester() === $semester) {
                    $infoItem->setToDoMandatory($mandatory);
                    break;
                }
            }
        }

        $toDoDeadline = $item->getDeadlineBySemester($semester);

        if (! empty($toDoDeadline)) {
            $infoItem
                ->setToDoDeadline($toDoDeadline)
                ->setDeadlineDate($toDoDeadline->getDeadDate());
        }
        return $infoItem;
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
        $completedItem = $this->em->getRepository('AppBundle:ToDoCompleted')->findOneBy(
            array(
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


    /**
     * @param Department $department
     * @param Semester $semester
     * @return ToDoItem[]
     */
    public function getCorrectList(Department $department, Semester $semester)
    {
        $repository = $this->em->getRepository('AppBundle:ToDoItem');
        $allToDoItems = $repository->findToDoListItemsBySemesterAndDepartment($semester, $department);

        dump($allToDoItems);
        $incompletedToDoItems = $this->getIncompletedToDoItems($allToDoItems, $semester, $department);
        $toDoShortDeadLines = $this->getToDoItemsWithShortDeadline($incompletedToDoItems);
        $toDoMandaoryNoDeadLine = $this->getMandatoryToDoItemsWithInsignificantDeadline($incompletedToDoItems, $semester);
        $toDoNonMandatoryNoDeadline = $this->getNonMandatoryToDoItemsWithInsignificantDeadline($incompletedToDoItems, $semester);
        $completedToDoListItems = $repository->findCompletedToDoListItems($semester);
        $correctOrder = array_merge($toDoShortDeadLines, $toDoMandaoryNoDeadLine, $toDoNonMandatoryNoDeadline, $completedToDoListItems);

        return $correctOrder;
    }
}
