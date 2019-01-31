<?php

namespace Tests\AppBundle\Service;

use AppBundle\Entity\AdmissionSubscriber;
use AppBundle\Entity\InfoMeeting;
use AppBundle\Service\EmailSender;
use Tests\SpooledEmailTestCase;

class EmailSenderTest extends SpooledEmailTestCase
{
    /** @var EmailSender */
    private $emailSender;

    public function setUp()
    {
        parent::setUp();
        $this->emailSender = self::$kernel->getContainer()->get(EmailSender::class);
    }

    /**
     * @throws \Exception
     */
    public function testSendInfoMeetingNotification()
    {
        $this->purgeSpool();
        $subscriber = new AdmissionSubscriber();
        $subscriber->setUnsubscribeCode('unsubcode');
        $subscriber->setInfoMeeting(true);

        $infoMeeting = $this->createBasicInfoMeeting();
        $infoMeeting->setDate((new \DateTime())->modify('+1 day'));

        $prevEmailCount = $this->getSpooledEmails()->count();
        $this->emailSender->sendInfoMeetingNotification($subscriber, $infoMeeting);
        $this->assertEquals($prevEmailCount + 1, $this->getSpooledEmails()->count());
    }

    /**
     * @return InfoMeeting
     * @throws \Exception
     */
    private function createBasicInfoMeeting(): InfoMeeting
    {
        $infoMeeting = new InfoMeeting();
        return $infoMeeting
            ->setDate((new \DateTime())->modify('+1 day'))
            ->setDescription('Det blir pizza!')
            ->setLink('https://facebook.com/infomøtevektor')
            ->setRoom('R2')
            ->setShowOnPage(true);
    }
}
