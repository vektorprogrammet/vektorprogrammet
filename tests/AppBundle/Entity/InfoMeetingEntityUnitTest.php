<?php

namespace Tests\AppBundle\Entity;


use AppBundle\Entity\InfoMeeting;
use PHPUnit\Framework\TestCase;

class InfoMeetingEntityUnitTest extends TestCase
{

    /**
     * @throws \Exception
     */
    public function testIsToday()
    {
        $infoMeeting = (new InfoMeeting())->setDate(new \DateTime());
        $this->assertTrue($infoMeeting->isToday());
    }

    /**
     * @throws \Exception
     */
    public function testIsTomorrow()
    {
        $infoMeeting = (new InfoMeeting())->setDate(
            (new \DateTime())->modify('+1 day')
        );
        $this->assertTrue($infoMeeting->isTomorrow());
    }
}
