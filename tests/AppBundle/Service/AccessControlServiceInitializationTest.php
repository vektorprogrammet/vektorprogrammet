<?php

namespace Tests\AppBundle\Service;

use AppBundle\Entity\AccessRule;
use AppBundle\Entity\UnhandledAccessRule;
use AppBundle\Entity\User;
use AppBundle\Service\AccessControlService;
use AppBundle\Service\RoleManager;
use AppBundle\Service\UserService;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Routing\RouterInterface;

final class AccessControlServiceInitializationTest extends TestCase
{
    public function testConstructionDoesNotReadTheDatabase(): void
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects($this->never())->method('getRepository');

        $this->assertInstanceOf(AccessControlService::class, $this->service($entityManager));
    }

    public function testFirstAccessCheckLoadsRulesOnceAndStillDeniesAnonymousUsers(): void
    {
        $rule = new AccessRule();
        $rule->setResource('restricted');
        $rule->setUsers([new User()]);

        $rules = $this->createMock(EntityRepository::class);
        $rules->expects($this->once())->method('findAll')->willReturn([$rule]);
        $unhandled = $this->createMock(EntityRepository::class);
        $unhandled->expects($this->once())->method('findAll')->willReturn([]);
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects($this->exactly(2))->method('getRepository')->willReturnMap([
            [AccessRule::class, $rules],
            [UnhandledAccessRule::class, $unhandled],
        ]);

        $service = $this->service($entityManager);
        $this->assertFalse($service->checkAccess('restricted'));
        $this->assertFalse($service->checkAccess('restricted'));
    }

    public function testDatabaseFailuresArePropagatedOnEveryAccessCheck(): void
    {
        $exception = new Exception('Database unavailable');
        $rules = $this->createMock(EntityRepository::class);
        $rules->expects($this->exactly(2))->method('findAll')->willThrowException($exception);
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects($this->exactly(2))->method('getRepository')
            ->with(AccessRule::class)->willReturn($rules);

        $service = $this->service($entityManager);
        for ($attempt = 0; $attempt < 2; ++$attempt) {
            try {
                $service->checkAccess('restricted');
                $this->fail('Unavailable rules must not grant access.');
            } catch (Exception $actual) {
                $this->assertSame($exception, $actual);
            }
        }
    }

    public function testPartiallyLoadedRulesAreRetriedAfterAnError(): void
    {
        $rule = new AccessRule();
        $rule->setResource('public');
        $rules = $this->createMock(EntityRepository::class);
        $rules->expects($this->exactly(2))->method('findAll')->willReturn([$rule]);
        $unhandled = $this->createMock(EntityRepository::class);
        $attempts = 0;
        $unhandled->expects($this->exactly(2))->method('findAll')->willReturnCallback(
            static function () use (&$attempts): array {
                if (++$attempts === 1) {
                    throw new Exception('Unable to load unhandled rules');
                }
                return [];
            }
        );
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects($this->exactly(4))->method('getRepository')->willReturnMap([
            [AccessRule::class, $rules],
            [UnhandledAccessRule::class, $unhandled],
        ]);

        $service = $this->service($entityManager);
        try {
            $service->checkAccess('public');
            $this->fail('A partially loaded cache must not be used.');
        } catch (Exception $exception) {
            $this->assertSame('Unable to load unhandled rules', $exception->getMessage());
        }
        $this->assertTrue($service->checkAccess('public'));
    }

    private function service(EntityManagerInterface $entityManager): AccessControlService
    {
        $userService = $this->createStub(UserService::class);
        $userService->method('getCurrentUser')->willReturn(null);

        return new AccessControlService(
            $entityManager,
            $this->createStub(RouterInterface::class),
            $this->createStub(RoleManager::class),
            $userService
        );
    }
}
