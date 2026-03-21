<?php

namespace App\Tests\App\Operations\Infrastructure\Entity;

use App\Identity\Infrastructure\Entity\User;
use App\Operations\Infrastructure\Entity\Receipt;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ReceiptUniqueVisualIdTest extends KernelTestCase
{
    public function testDuplicateVisualIdThrowsUniqueConstraintViolation(): void
    {
        self::bootKernel();
        $em = self::getContainer()->get('doctrine.orm.entity_manager');

        $visualId = 'test-unique-visual-' . uniqid();

        $receipt1 = new Receipt();
        $receipt1->setVisualId($visualId);
        $receipt1->setDescription('Test receipt 1');
        $receipt1->setSum(100.0);
        $em->persist($receipt1);
        $em->flush();

        $this->expectException(UniqueConstraintViolationException::class);

        $receipt2 = new Receipt();
        $receipt2->setVisualId($visualId);
        $receipt2->setDescription('Test receipt 2');
        $receipt2->setSum(200.0);
        $em->persist($receipt2);
        $em->flush();
    }
}
