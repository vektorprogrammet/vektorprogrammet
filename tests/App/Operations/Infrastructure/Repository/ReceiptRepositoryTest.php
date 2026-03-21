<?php

declare(strict_types=1);

namespace App\Tests\App\Operations\Infrastructure\Repository;

use App\Operations\Infrastructure\Entity\Receipt;
use App\Operations\Infrastructure\Repository\ReceiptRepository;
use App\Organization\Infrastructure\Entity\Department;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ReceiptRepositoryTest extends KernelTestCase
{
    private ReceiptRepository $repo;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->repo = self::getContainer()->get(ReceiptRepository::class);
    }

    public function testFindByDepartmentReturnsOnlyDepartmentReceipts(): void
    {
        /** @var Department $department */
        $department = self::getContainer()->get('doctrine')
            ->getRepository(Department::class)
            ->findOneBy([]);

        if ($department === null) {
            $this->markTestSkipped('No department in fixtures');
        }

        $receipts = $this->repo->findByDepartment($department);
        $this->assertNotEmpty($receipts, 'Fixtures should contain receipts for this department');

        foreach ($receipts as $receipt) {
            $user = $receipt->getUser();
            $this->assertNotNull($user->getDepartment(), 'Receipt user must have a department');
            $this->assertSame(
                $department->getId(),
                $user->getDepartment()->getId(),
                'Receipt must belong to the queried department'
            );
        }
    }

    public function testFindByDepartmentWithStatusFilter(): void
    {
        /** @var Department $department */
        $department = self::getContainer()->get('doctrine')
            ->getRepository(Department::class)
            ->findOneBy([]);

        if ($department === null) {
            $this->markTestSkipped('No department in fixtures');
        }

        $receipts = $this->repo->findByDepartment($department, Receipt::STATUS_PENDING);

        foreach ($receipts as $receipt) {
            $this->assertSame(Receipt::STATUS_PENDING, $receipt->getStatus());
        }
    }
}
