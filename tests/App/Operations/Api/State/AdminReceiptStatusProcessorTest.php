<?php

namespace App\Tests\App\Operations\Api\State;

use App\Identity\Infrastructure\AccessControlService;
use App\Identity\Infrastructure\Entity\User;
use App\Operations\Api\Resource\AdminReceiptStatusInput;
use App\Operations\Api\State\AdminReceiptStatusProcessor;
use App\Operations\Infrastructure\Entity\Receipt;
use App\Operations\Infrastructure\Repository\ReceiptRepository;
use App\Organization\Infrastructure\Entity\Department;
use ApiPlatform\Metadata\Put;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Bundle\SecurityBundle\Security;

class AdminReceiptStatusProcessorTest extends TestCase
{
    public function testDepartmentAuthorizationIsEnforced(): void
    {
        $department = $this->createMock(Department::class);

        $receipt = $this->createMock(Receipt::class);
        $receipt->method('getUser')->willReturn($this->createConfiguredMock(User::class, [
            'getDepartment' => $department,
        ]));

        $repo = $this->createMock(ReceiptRepository::class);
        $repo->method('find')->willReturn($receipt);

        $accessControl = $this->createMock(AccessControlService::class);
        $accessControl->method('assertDepartmentAccess')
            ->willThrowException(new AccessDeniedHttpException('You do not have access to this department.'));

        $security = $this->createMock(Security::class);
        $security->method('getUser')->willReturn($this->createMock(User::class));

        $processor = new AdminReceiptStatusProcessor(
            $repo,
            $this->createMock(EntityManagerInterface::class),
            $this->createMock(EventDispatcherInterface::class),
            $accessControl,
            $security,
        );

        $input = new AdminReceiptStatusInput();
        $input->status = 'refunded';

        $this->expectException(AccessDeniedHttpException::class);
        $processor->process($input, new Put(), ['id' => 1]);
    }

    public function testInvalidTransitionReturns422(): void
    {
        $department = $this->createMock(Department::class);
        $user = $this->createMock(User::class);
        $user->method('getDepartment')->willReturn($department);

        $receipt = $this->createMock(Receipt::class);
        $receipt->method('getUser')->willReturn($user);
        $receipt->method('setStatus')->willThrowException(
            new \InvalidArgumentException('Invalid receipt status transition from refunded to rejected')
        );

        $repo = $this->createMock(ReceiptRepository::class);
        $repo->method('find')->willReturn($receipt);

        $accessControl = $this->createMock(AccessControlService::class);
        // assertDepartmentAccess does not throw — auth passes, then transition fails
        $security = $this->createMock(Security::class);
        $security->method('getUser')->willReturn($user);

        $processor = new AdminReceiptStatusProcessor(
            $repo,
            $this->createMock(EntityManagerInterface::class),
            $this->createMock(EventDispatcherInterface::class),
            $accessControl,
            $security,
        );

        $input = new AdminReceiptStatusInput();
        $input->status = 'rejected';

        $this->expectException(UnprocessableEntityHttpException::class);
        $processor->process($input, new Put(), ['id' => 1]);
    }

    public function testNullDepartmentDeniesNonAdmin(): void
    {
        $user = $this->createMock(User::class);
        $user->method('getDepartment')->willReturn(null);

        $receipt = $this->createMock(Receipt::class);
        $receipt->method('getUser')->willReturn($user);

        $repo = $this->createMock(ReceiptRepository::class);
        $repo->method('find')->willReturn($receipt);

        $accessControl = $this->createMock(AccessControlService::class);
        $security = $this->createMock(Security::class);
        $security->method('getUser')->willReturn($user);
        $security->method('isGranted')->with('ROLE_ADMIN')->willReturn(false);

        $processor = new AdminReceiptStatusProcessor(
            $repo,
            $this->createMock(EntityManagerInterface::class),
            $this->createMock(EventDispatcherInterface::class),
            $accessControl,
            $security,
        );

        $input = new AdminReceiptStatusInput();
        $input->status = 'refunded';

        $this->expectException(AccessDeniedHttpException::class);
        $processor->process($input, new Put(), ['id' => 1]);
    }
}
