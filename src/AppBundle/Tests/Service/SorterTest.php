<?php

namespace AppBundle\Tests\Command;

use AppBundle\Service\Sorter;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use AppBundle\Entity\Receipt;
use AppBundle\Entity\User;

class SorterTest extends KernelTestCase
{
    /** @var  Sorter */
    private $sorter;

    /** @var  User[] */
    private $mockUsers;

    /** @var  Receipt[] */
    private $mockReceipts;

    protected function setUp()
    {
        $kernel = $this->createKernel();
        $kernel->boot();

        $this->sorter = $kernel->getContainer()->get('app.sorter');

        $this->mockReceipts = [
            new Receipt(),
            new Receipt(),
            new Receipt(),
            new Receipt(),
        ];

        $this->mockUsers = [
            new User(),
            new User(),
            new User(),
        ];

        $this->mockReceipts[0]->setSubmitDate(new \DateTime('2015-09-05'));
        $this->mockReceipts[1]->setSubmitDate(new \DateTime('2016-09-05'));
        $this->mockReceipts[2]->setSubmitDate(new \DateTime('2017-09-05'));
        $this->mockReceipts[3]->setSubmitDate(new \DateTime('2017-09-05'));

        $this->mockReceipts[0]->setStatus(Receipt::STATUS_REFUNDED);
        $this->mockReceipts[1]->setStatus(Receipt::STATUS_PENDING);
        $this->mockReceipts[2]->setStatus(Receipt::STATUS_REJECTED);
        $this->mockReceipts[3]->setStatus(Receipt::STATUS_PENDING);

        foreach ($this->mockReceipts as $receipt) {
            $this->mockUsers[0]->addReceipt($receipt);
        }

        $user2Receipt = new Receipt();
        $user3Receipt = new Receipt();

        $user2Receipt->setStatus(Receipt::STATUS_REFUNDED);
        $user3Receipt->setStatus(Receipt::STATUS_PENDING);

        $user3Receipt->setSubmitDate(new \DateTime('2013-09-05'));

        // Add receipts to users instead of setting users of the receipts
        // with $user->addReceipt()
        $this->mockUsers[1]->addReceipt($user2Receipt);
        $this->mockUsers[2]->addReceipt($user3Receipt);
    }

    public function testSortReceipts()
    {
        $this->sorter->sortReceiptsBySubmitTime($this->mockReceipts);
        $this->sorter->sortReceiptsByStatus($this->mockReceipts);
        $this->assertEquals(new \DateTime('2017-09-05'), $this->mockReceipts[0]->getSubmitDate());
        $this->assertEquals(new \DateTime('2016-09-05'), $this->mockReceipts[1]->getSubmitDate());

        $this->assertEquals(Receipt::STATUS_PENDING, $this->mockReceipts[0]->getStatus());
        $this->assertEquals(Receipt::STATUS_REFUNDED, $this->mockReceipts[3]->getStatus());
    }

    public function testSortUsers()
    {
        $this->sorter->sortUsersByReceiptSubmitTime($this->mockUsers);
        $this->sorter->sortUsersByReceiptStatus($this->mockUsers);


        $this->assertEquals(Receipt::STATUS_PENDING, $this->mockUsers[1]->getReceipts()[0]->getStatus());
        $this->assertEquals(new \DateTime('2013-09-05'), $this->mockUsers[1]->getReceipts()[0]->getSubmitDate());

        $this->assertEquals(Receipt::STATUS_REFUNDED, $this->mockUsers[2]->getReceipts()[0]->getStatus());
    }
}
