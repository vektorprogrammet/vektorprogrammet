<?php

namespace Tests\AppBundle\Service;

use AppBundle\Entity\Survey;
use AppBundle\Entity\SurveyNotificationCollection;
use AppBundle\Entity\UserGroup;
use AppBundle\Mailer\MailerInterface;
use AppBundle\Service\SurveyNotifier;
use AppBundle\Sms\SmsSenderInterface;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;

class SurveyNotifierTest extends TestCase
{
    /**
     * @var SurveyNotifier
     */
    private $service;

    /**
     * @var EntityManagerInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private $em;

    protected function setUp()
    {
        $this->em = $this->createMock(EntityManagerInterface::class);
        $mailer = $this->createMock(MailerInterface::class);
        $twig = $this->createMock(Environment::class);
        $logger = $this->createMock(LoggerInterface::class);
        $router = $this->createMock(RouterInterface::class);
        $smsSender = $this->createMock(SmsSenderInterface::class);

        $this->service = new SurveyNotifier(
            'from@example.com',
            $mailer,
            $twig,
            $logger,
            $this->em,
            $router,
            $smsSender
        );
    }

    public function testInitializeSurveyNotifier()
    {
        $collection = new SurveyNotificationCollection();
        $userGroup = new UserGroup();
        $collection->addUserGroup($userGroup);

        $this->em->expects($this->once())
            ->method('persist')
            ->with($userGroup);

        $this->em->expects($this->once())
            ->method('persist')
            ->with($collection);

        $this->em->expects($this->once())
            ->method('flush');

        $this->service->initializeSurveyNotifier($collection);

        $this->assertTrue($userGroup->getActive());
        $this->assertFalse($userGroup->getUserGroupCollection()->getDeletable());
    }
}

