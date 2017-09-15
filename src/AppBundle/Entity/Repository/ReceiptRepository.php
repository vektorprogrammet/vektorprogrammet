<?php

namespace AppBundle\Entity\Repository;

use AppBundle\Entity\Department;
use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\User;
use AppBundle\Entity\Receipt;

class ReceiptRepository extends EntityRepository
{
    /**
     * @param User $user
     *
     * @return Receipt[]
     */
    public function findByUser(User $user)
    {
        $receipts = $this->createQueryBuilder('receipt')
            ->select('receipt')
            ->where('receipt.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();

        usort($receipts, function (Receipt $a, Receipt $b) {
            if ($a->getStatus() === Receipt::STATUS_PENDING && $b->getStatus() !== Receipt::STATUS_PENDING) {
                return -1;
            } elseif ($b->getStatus() === Receipt::STATUS_PENDING && $a->getStatus() !== Receipt::STATUS_PENDING) {
                return 1;
            } elseif ($a->getStatus() === Receipt::STATUS_REFUNDED && $b->getStatus() !== Receipt::STATUS_REFUNDED) {
                return -1;
            } elseif ($b->getStatus() === Receipt::STATUS_REFUNDED && $a->getStatus() !== Receipt::STATUS_REFUNDED) {
                return 1;
            } else {
                return $a->getSubmitDate()->getTimestamp() - $b->getSubmitDate()->getTimestamp();
            }
        });

        return $receipts;
    }

    /**
     * @param User $user
     *
     * @return Receipt[]
     */
    public function findActiveByUser(User $user)
    {
        return $this->createQueryBuilder('receipt')
            ->select('receipt')
            ->where('receipt.user = :user')
            ->andWhere('receipt.status = :status')
            ->setParameter('user', $user)
            ->setParameter('status', Receipt::STATUS_PENDING)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param User $user
     *
     * @return Receipt[]
     */
    public function findInactiveByUser(User $user)
    {
        return $this->createQueryBuilder('receipt')
            ->select('receipt')
            ->where('receipt.user = :user')
            ->andWhere('receipt.status != :status')
            ->setParameter('user', $user)
            ->setParameter('status', Receipt::STATUS_PENDING)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param Department $department
     *
     * @return Receipt[]
     */
    public function findByDepartment(Department $department)
    {
        return $this->createQueryBuilder('receipt')
            ->select('receipt')
            ->join('receipt.user', 'user')
            ->join('user.fieldOfStudy', 'fos')
            ->where('fos.department = :department')
            ->setParameter('department', $department)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param Department $department
     *
     * @return Receipt[]
     */
    public function findActiveByDepartment(Department $department)
    {
        return $this->createQueryBuilder('receipt')
            ->select('receipt')
            ->join('receipt.user', 'user')
            ->join('user.fieldOfStudy', 'fos')
            ->where('fos.department = :department')
            ->andWhere('receipt.status = :status')
            ->setParameter('department', $department)
            ->setParameter('status', Receipt::STATUS_PENDING)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param Department $department
     *
     * @return Receipt[]
     */
    public function findInactiveByDepartment(Department $department)
    {
        return $this->createQueryBuilder('receipt')
            ->select('receipt')
            ->join('receipt.user', 'user')
            ->join('user.fieldOfStudy', 'fos')
            ->where('fos.department = :department')
            ->andWhere('receipt.status != :status')
            ->setParameter('department', $department)
            ->setParameter('status', Receipt::STATUS_PENDING)
            ->getQuery()
            ->getResult();
    }
}
