<?php

namespace AppBundle\Service;

use AppBundle\Entity\Department;
use AppBundle\Entity\Semester;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;

use AppBundle\Entity\TodoItem;
use AppBundle\Entity\TodoMandatory;
use AppBundle\Entity\TodoDeadline;
use AppBundle\Entity\TodoCompleted;
use AppBundle\Model\TodoItemInfo;

class TodoListService
{
    /**
     * @var EntityManagerInterface
     *
     */
    private $em;


    /**
     * TodoListService constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }


    /**
     * @param TodoItem $item
     * @param Department $department
     * @return bool
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function itemHasDeadlineThisSemesterByDepartment(TodoItem $item, Department $department)
    {
        if (empty($item->getTodoDeadlines())) {
            return false;
        } else {
            $currentSemester = $this->em->getRepository('AppBundle:Semester')->findCurrentSemester();
            $deadlines = $item->getTodoDeadlines();
            return ($deadlines[0]->getSemester()->getId() == $currentSemester->getId());
        }
    }

    /**
     * @param TodoItem[] $todoItems
     * @return TodoItem[]
     */
    public function sortByPriority(array $todoItems)
    {
        $sortedArray = $todoItems;
        usort($sortedArray, function (TodoItem $a, TodoItem $b) {
            return ($a->getPriority() < $b->getPriority());
        });
        return $sortedArray;
    }

    /**
     * @param TodoItem[] $todoItems
     * @param Semester $semester
     * @return TodoItem[]
     */
    public function getMandatoryTodoItems(array $todoItems, $semester)
    {
        $mandatoryItems = array_filter($todoItems, function (TodoItem $a) use ($semester) {
            return ($a->isMandatoryBySemester($semester));
        });

        return $mandatoryItems;
    }


    /**
     * @param TodoItem $item
     * @param Semester $semester
     * @return TodoMandatory|null
     */
    public function getMandatoryBySemester(TodoItem $item, Semester $semester) : ? TodoMandatory
    {
        if (empty($item->getTodoMandatories())) {
            return null;
        }
        $mandatories = $item->getTodoMandatories();
        foreach ($mandatories as $mandatory) {
            if ($mandatory->getSemester() === $semester) {
                return $mandatory;
            }
        }
        return null;
    }

    /**
     * @param TodoItem $a
     * @return bool
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function hasDeadLineShortly(TodoItem $a)
    {
        return ($a->hasShortDeadlineBySemester($this->em->getRepository('AppBundle:Semester')->findCurrentSemester()));
    }

    /**
     * @param TodoItem[] $todoItems
     * @return TodoItem[]
     */
    public function getTodoItemsWithShortDeadline(array $todoItems)
    {
        $items = array_filter($todoItems, array($this, "hasDeadLineShortly"));
        usort($items, function (TodoItem $a, TodoItem $b) {
            return ($a->getTodoDeadlines()[0]->getDeadDate() > $b->getTodoDeadlines()[0]->getDeadDate());
        });
        return $items;
    }

    /**
     * @param TodoItem[] $todoItems
     * @param Semester $semester
     * @return TodoItem[]
     */
    public function getMandatoryTodoItemsWithInsignificantDeadline(array $todoItems, Semester $semester)
    {
        $items = array_filter($todoItems, function (TodoItem $a) use ($semester) {
            return ($a->isMandatoryBySemester($semester) && !($this->hasDeadLineShortly($a)));
        });
        return $this->sortByPriority($items);
    }

    /**
     * @param TodoItem[] $todoItems
     * @param Semester $semester
     * @return TodoItem[]
     */
    public function getNonMandatoryTodoItemsWithInsignificantDeadline(array $todoItems, Semester $semester)
    {
        $items = array_filter($todoItems, function (TodoItem $a) use ($semester) {
            return !($a->isMandatoryBySemester($semester) || ($this->hasDeadLineShortly($a)));
        });
        return $this->sortByPriority($items);
    }

    /**
     * @param TodoItem[] $todoItems
     * @param Semester $semester
     * @param Department $department
     * @return TodoItem[]
     */
    public function getIncompletedTodoItems(array $todoItems, Semester $semester, Department $department)
    {
        $items = array_filter($todoItems, function (TodoItem $a) use ($semester, $department) {
            return !($a->isCompletedInSemesterByDepartment($semester, $department));
        });
        return array_values($items);
    }

    /**
     * @param TodoItemInfo $itemInfo
     * @param EntityManagerInterface $em
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function generateEntities(TodoItemInfo $itemInfo)
    {
        $todoItem = new TodoItem();
        $department = $itemInfo->getDepartment();
        $todoItem
            ->setCreatedAt(new DateTime())
            ->setPriority($itemInfo->getPriority())
            ->setTitle($itemInfo->getTitle())
            ->setDescription($itemInfo->getDescription())
            ->setSemester($itemInfo->getSemester())
            ->setDepartment($department);

        $this->em->persist($todoItem);

        $correctSemester = empty($itemInfo->getSemester()) ?
            $this->em->getRepository('AppBundle:Semester')->findCurrentSemester() :
            $itemInfo->getSemester();

        if ($itemInfo->getIsMandatory()) {
            $todoMandatory = new TodoMandatory();
            $todoMandatory
                ->setTodoItem($todoItem)
                ->setIsMandatory(true)
                ->setSemester($correctSemester);
            $this->em->persist($todoMandatory);
        }

        $deadlineDate = $itemInfo->getDeadlineDate();
        if ($deadlineDate !== null) {
            $todoDeadLine = new TodoDeadline();
            $todoDeadLine
                ->setTodoItem($todoItem)
                ->setSemester($correctSemester)
                ->setDeadDate($deadlineDate);

            $this->em->persist($todoDeadLine);
        }
        $this->em->flush();
    }


    /**
     * @param TodoItemInfo $itemInfo
     * @param Semester $semester
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function editEntities(TodoItemInfo $itemInfo, Semester $semester)
    {
        $todoItem = $itemInfo->getTodoItem();
        $todoItem
            ->setPriority($itemInfo->getPriority())
            ->setTitle($itemInfo->getTitle())
            ->setDescription($itemInfo->getDescription())
            ->setSemester($itemInfo->getSemester())
            ->setDepartment($itemInfo->getDepartment());

        $this->em->persist($todoItem);

        $preExistingMandatory = $itemInfo->getTodoMandatory();
        $previousMandatoryStatus = empty($preExistingMandatory) ? false : $preExistingMandatory->isMandatory();
        $sr  = $this->em->getRepository('AppBundle:Semester');
        $currentSemester = $sr->findCurrentSemester();


        if ($itemInfo->getIsMandatory() !== $previousMandatoryStatus) {
            if (empty($preExistingMandatory)) {
                $currentTodoMandatory = new TodoMandatory();
                $currentTodoMandatory
                    ->setIsMandatory($itemInfo->getIsMandatory())
                    ->setSemester($semester)
                    ->setTodoItem($todoItem);
                $this->em->persist($currentTodoMandatory);
            } else {
                $preExistingMandatory->setIsMandatory($itemInfo->getIsMandatory());
                $this->em->persist($preExistingMandatory);
            }

            if ($semester !== $currentSemester) {
                $nextSemester = $sr->getNextActive($semester);
                $nextMandatory = $this->getMandatoryBySemester($todoItem, $nextSemester);
                if (empty($nextMandatory)) {
                    $newNextMandatory = new TodoMandatory();
                    $newNextMandatory
                        ->setIsMandatory(!($itemInfo->getIsMandatory()))
                        ->setSemester($nextSemester)
                        ->setTodoItem($todoItem);
                    $this->em->persist($newNextMandatory);
                }
            }
        }

        $deadlineDate = $itemInfo->getDeadlineDate();
        $previousDeadLine = $todoItem->getDeadlineBySemester($semester);
        if ($deadlineDate !== null) {
            if (empty($previousDeadLine)) {
                $todoDeadLine = new TodoDeadline();
                $todoDeadLine
                    ->setTodoItem($todoItem)
                    ->setSemester($currentSemester)
                    ->setDeadDate($deadlineDate);

                $this->em->persist($todoDeadLine);
            } else {
                $previousDeadLine->setDeadDate($itemInfo->getDeadlineDate());
                $this->em->persist($previousDeadLine);
            }
        } elseif (!empty($previousDeadLine)) {
            $this->em->remove($previousDeadLine);
        }
        $this->em->flush();
    }

    /**
     * @param TodoItem $item
     * @param Semester $semester
     * @return TodoItemInfo
     */
    public function createTodoItemInfoFromItem(TodoItem $item, Semester $semester)
    {
        $infoItem = new TodoItemInfo();
        $infoItem
            ->setSemester($item->getSemester())
            ->setDepartment($item->getDepartment())
            ->setPriority($item->getPriority())
            ->setTitle($item->getTitle())
            ->setDescription($item->getDescription())
            ->setTodoItem($item);

        $mandatory = $this->getMandatoryBySemester($item, $semester);
        $infoItem->setIsMandatory(empty($mandatory) ? false : $mandatory->isMandatory());

        if ($infoItem->getIsMandatory()) {
            $mandatoryItems = $item->getTodoMandatories();
            foreach ($mandatoryItems as $mandatory) {
                if ($mandatory->getSemester() === $semester) {
                    $infoItem->setTodoMandatory($mandatory);
                    break;
                }
            }
        }

        $todoDeadline = $item->getDeadlineBySemester($semester);

        if (! empty($todoDeadline)) {
            $infoItem
                ->setTodoDeadline($todoDeadline)
                ->setDeadlineDate($todoDeadline->getDeadDate());
        }
        return $infoItem;
    }

    /**
     * @param TodoItem $item
     * @param Semester $semester
     * @param Department $department
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function completedItem(TodoItem $item, Semester $semester, Department $department)
    {
        $completedItem = new TodoCompleted();
        $completedItem
            ->setTodoItem($item)
            ->setSemester($semester)
            ->setCompletedAt(new DateTime())
            ->setDepartment($department);

        $this->em->persist($completedItem);
        $this->em->flush();
    }


    /**
     * @param TodoItem $item
     * @param Semester $semester
     * @param Department $department
     * @return bool
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function toggleCompletedItem(TodoItem $item, Semester $semester, Department $department)
    {
        $completedItem = $this->em->getRepository('AppBundle:TodoCompleted')->findOneBy(
            array(
                'semester' => $semester,
                'department' => $department,
                'todoItem' => $item,
            )
        );

        if ($completedItem !== null) {
            $this->em->remove($completedItem);
            $this->em->flush();
            return true;
        } else {
            $completedItem = new TodoCompleted();
            $completedItem
                ->setCompletedAt(new DateTime())
                ->setSemester($semester)
                ->setDepartment($department)
                ->setTodoItem($item);
            $this->em->persist($completedItem);
            $this->em->flush();

            return true;
        }
    }


    /**
     * @param Department $department
     * @param Semester $semester
     * @return TodoItem[]
     */
    public function getOrderedList(Department $department, Semester $semester)
    {
        $repository = $this->em->getRepository('AppBundle:TodoItem');
        $allTodoItems = $repository->findTodoListItemsBySemesterAndDepartment($semester, $department);
        $incompletedTodoItems = $this->getIncompletedTodoItems($allTodoItems, $semester, $department);
        $todoShortDeadLines = $this->getTodoItemsWithShortDeadline($incompletedTodoItems);
        $todoMandaoryNoDeadLine = $this->getMandatoryTodoItemsWithInsignificantDeadline($incompletedTodoItems, $semester);
        $todoNonMandatoryNoDeadline = $this->getNonMandatoryTodoItemsWithInsignificantDeadline($incompletedTodoItems, $semester);
        $completedTodoListItems = $repository->findCompletedTodoListItemsBySemesterAndDepartment($semester, $department);
        $orderedList = array_merge($todoShortDeadLines, $todoMandaoryNoDeadLine, $todoNonMandatoryNoDeadline, $completedTodoListItems);

        return $orderedList;
    }
}
