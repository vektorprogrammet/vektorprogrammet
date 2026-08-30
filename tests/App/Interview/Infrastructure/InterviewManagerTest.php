<?php

declare(strict_types=1);

namespace App\Tests\App\Interview\Infrastructure;

use App\Identity\Infrastructure\Entity\User;
use App\Interview\Infrastructure\Entity\Interview;
use App\Interview\Infrastructure\InterviewManager;
use App\Interview\Infrastructure\Repository\InterviewRepository;
use App\Support\Infrastructure\Mailer\MailerInterface;
use App\Support\Infrastructure\Sms\SmsSenderInterface;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\Clock\MockClock;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Twig\Environment;

final class InterviewManagerTest extends TestCase
{
    public function testAcceptInterviewReminderUsesTheInjectedClockForSelectionAndDelivery(): void
    {
        $clock = new MockClock('2026-08-28 12:00:00 Europe/Oslo');
        $applicant = new User();
        $applicant->setEmail('clocked-reminder@example.invalid');
        $applicant->setFirstName('Clocked');
        $applicant->setLastName('Reminder');
        $applicant->setPhone('00000000');

        $interview = new Interview();
        $interview->setUser($applicant);
        $interview->setScheduled($clock->now()->modify('+2 days'));
        $lastScheduleChanged = new \ReflectionProperty(Interview::class, 'lastScheduleChanged');
        $lastScheduleChanged->setValue($interview, $clock->now()->modify('-2 days'));

        $repository = $this->createMock(InterviewRepository::class);
        $repository->expects($this->once())
            ->method('findAcceptInterviewNotificationRecipients')
            ->with($this->equalTo($clock->now()))
            ->willReturn([$interview]);

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects($this->once())
            ->method('getRepository')
            ->with(Interview::class)
            ->willReturn($repository);
        $entityManager->expects($this->once())->method('persist')->with($interview);
        $entityManager->expects($this->once())->method('flush');

        $mailer = $this->createMock(MailerInterface::class);
        $mailer->expects($this->once())
            ->method('send')
            ->with($this->isInstanceOf(Email::class));

        $twig = $this->createMock(Environment::class);
        $twig->method('render')->willReturn('<p>Reminder</p>');

        $smsSender = $this->createMock(SmsSenderInterface::class);
        $smsSender->expects($this->never())->method('send');

        $manager = new InterviewManager(
            $this->createMock(TokenStorageInterface::class),
            $this->createMock(AuthorizationCheckerInterface::class),
            $mailer,
            $twig,
            $clock,
            $this->createMock(LoggerInterface::class),
            $entityManager,
            $this->createMock(RouterInterface::class),
            $smsSender,
        );

        $manager->sendAcceptInterviewReminders();

        $this->assertSame(1, $interview->getNumAcceptInterviewRemindersSent());
    }
}
