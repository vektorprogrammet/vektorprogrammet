<?php

declare(strict_types=1);

namespace App\Operations\Infrastructure\Repository;

use App\Operations\Infrastructure\Entity\Receipt;
use App\Identity\Infrastructure\Entity\User;
use App\Organization\Infrastructure\Entity\Department;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ReceiptRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Receipt::class);
    }

    /**
     * @return Receipt[]
     */
    public function findByUser(User $user)
    {
        return $this->createQueryBuilder('receipt')
            ->select('receipt')
            ->where('receipt.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Receipt[]
     */
    public function findByStatus(string $status)
    {
        return $this->createQueryBuilder('receipt')
            ->select('receipt')
            ->where('receipt.status = :status')
            ->setParameter('status', $status)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Receipt[]
     */
    public function findByUserOrdered(User $user, ?string $status = null): array
    {
        $qb = $this->createQueryBuilder('receipt')
            ->where('receipt.user = :user')
            ->setParameter('user', $user)
            ->orderBy('receipt.submitDate', 'DESC');

        if ($status !== null) {
            $qb->andWhere('receipt.status = :status')
                ->setParameter('status', $status);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @return Receipt[]
     */
    public function findByDepartment(Department $department, ?string $status = null): array
    {
        $qb = $this->createQueryBuilder('receipt')
            ->join('receipt.user', 'u')
            ->join('u.fieldOfStudy', 'fos')
            ->join('fos.department', 'd')
            ->where('d = :department')
            ->setParameter('department', $department)
            ->orderBy('receipt.submitDate', 'DESC');

        if ($status !== null) {
            $qb->andWhere('receipt.status = :status')
                ->setParameter('status', $status);
        }

        return $qb->getQuery()->getResult();
    }
}
