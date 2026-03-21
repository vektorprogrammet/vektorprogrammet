<?php

namespace App\Tests\App\Identity\Infrastructure;

use App\Identity\Infrastructure\Entity\PasswordReset;
use App\Identity\Infrastructure\Repository\PasswordResetRepository;
use App\Identity\Infrastructure\PasswordManager;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

/**
 * Tests that the expiry check in PasswordManager correctly enforces the 24h TTL.
 */
class PasswordManagerExpiryTest extends TestCase
{
    private function makeManager(PasswordReset $passwordReset): PasswordManager
    {
        $repo = $this->createMock(PasswordResetRepository::class);
        $repo->method('findPasswordResetByHashedResetCode')->willReturn($passwordReset);
        $repo->method('deletePasswordResetByHashedResetCode');

        $em = $this->createMock(EntityManagerInterface::class);
        $em->method('getRepository')->willReturn($repo);

        $mailer = $this->createMock(\App\Support\Infrastructure\Mailer\MailerInterface::class);
        $twig = $this->createMock(\Twig\Environment::class);

        return new PasswordManager($em, $mailer, $twig);
    }

    public function testTokenCreated25HoursAgoIsExpired(): void
    {
        $reset = new PasswordReset();
        $reset->setResetTime(new \DateTime('-25 hours'));

        $manager = $this->makeManager($reset);
        $this->assertTrue($manager->resetCodeHasExpired('any-code'), 'Token created 25h ago should be expired');
    }

    public function testTokenCreated1HourAgoIsNotExpired(): void
    {
        $reset = new PasswordReset();
        $reset->setResetTime(new \DateTime('-1 hour'));

        $manager = $this->makeManager($reset);
        $this->assertFalse($manager->resetCodeHasExpired('any-code'), 'Token created 1h ago should not be expired');
    }
}
