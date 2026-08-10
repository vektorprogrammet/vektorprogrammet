<?php

namespace App\Tests\App\Operations\Infrastructure\Entity;

use App\Operations\Infrastructure\Entity\Receipt;
use PHPUnit\Framework\TestCase;

/**
 * `Receipt::$visualId` is declared unique in the database, so the value the constructor
 * assigns has to be unique too -- otherwise the constraint turns an ordinary receipt
 * submission into a failed insert.
 *
 * It did not use to be: the id was `dechex(milliseconds)`, so every receipt created inside
 * the same millisecond shared one id. Loading the fixtures produced eight identical ids and
 * broke the whole seed.
 */
class ReceiptVisualIdGenerationTest extends TestCase
{
    public function testReceiptsCreatedInTheSameMillisecondGetDistinctVisualIds(): void
    {
        $ids = [];
        for ($i = 0; $i < 50; ++$i) {
            $ids[] = (new Receipt())->getVisualId();
        }

        $this->assertCount(
            50,
            array_unique($ids),
            'Receipts created back-to-back must not share a visualId; the column is unique'
        );
    }

    public function testVisualIdIsHexadecimal(): void
    {
        $this->assertMatchesRegularExpression('/^[0-9a-f]+$/', (new Receipt())->getVisualId());
    }
}
