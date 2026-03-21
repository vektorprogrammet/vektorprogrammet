<?php

namespace App\Tests\App\Interview\Infrastructure\Entity;

use App\Interview\Infrastructure\Entity\Interview;
use PHPUnit\Framework\TestCase;

class InterviewConstructorTest extends TestCase
{
    public function testNewInterviewHasNullConducted(): void
    {
        $interview = new Interview();

        $this->assertNull($interview->getConducted(), 'A newly created Interview must have conducted === null');
    }
}
