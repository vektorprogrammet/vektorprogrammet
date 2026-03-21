<?php

declare(strict_types=1);

namespace App\Tests\App\Operations\Api\State;

use ApiPlatform\Metadata\GetCollection;
use App\Identity\Infrastructure\Entity\User;
use App\Operations\Api\Resource\AdminReceiptListResource;
use App\Operations\Api\State\AdminReceiptListProvider;
use App\Operations\Infrastructure\Entity\Receipt;
use App\Operations\Infrastructure\Repository\ReceiptRepository;
use App\Organization\Infrastructure\Entity\Department;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class AdminReceiptListProviderTest extends TestCase
{
    public function testReturnsDepartmentScopedReceipts(): void
    {
        $department = $this->createMock(Department::class);

        $user = $this->createMock(User::class);
        $user->method('getDepartment')->willReturn($department);
        $user->method('getFullName')->willReturn('Test User');

        $receipt = $this->createMock(Receipt::class);
        $receipt->method('getId')->willReturn(1);
        $receipt->method('getVisualId')->willReturn('abc123');
        $receipt->method('getUser')->willReturn($user);
        $receipt->method('getDescription')->willReturn('Bus ticket');
        $receipt->method('getSum')->willReturn(150.0);
        $receipt->method('getReceiptDate')->willReturn(new \DateTime('2026-01-10'));
        $receipt->method('getSubmitDate')->willReturn(new \DateTime('2026-01-10'));
        $receipt->method('getStatus')->willReturn(Receipt::STATUS_PENDING);

        $repo = $this->createMock(ReceiptRepository::class);
        $repo->expects($this->once())
            ->method('findByDepartment')
            ->with($department, null)
            ->willReturn([$receipt]);

        $security = $this->createMock(Security::class);
        $security->method('getUser')->willReturn($user);

        $provider = new AdminReceiptListProvider($repo, $security);
        $result = $provider->provide(new GetCollection(), []);

        $this->assertCount(1, $result);
        $this->assertInstanceOf(AdminReceiptListResource::class, $result[0]);
        $this->assertSame(1, $result[0]->id);
        $this->assertSame('abc123', $result[0]->visualId);
        $this->assertSame(Receipt::STATUS_PENDING, $result[0]->status);
    }

    public function testPassesStatusFilterToRepository(): void
    {
        $department = $this->createMock(Department::class);
        $user = $this->createMock(User::class);
        $user->method('getDepartment')->willReturn($department);

        $repo = $this->createMock(ReceiptRepository::class);
        $repo->expects($this->once())
            ->method('findByDepartment')
            ->with($department, 'pending')
            ->willReturn([]);

        $security = $this->createMock(Security::class);
        $security->method('getUser')->willReturn($user);

        $provider = new AdminReceiptListProvider($repo, $security);
        $result = $provider->provide(new GetCollection(), [], ['filters' => ['status' => 'pending']]);

        $this->assertSame([], $result);
    }

    public function testThrowsWhenNotAuthenticated(): void
    {
        $repo = $this->createMock(ReceiptRepository::class);
        $security = $this->createMock(Security::class);
        $security->method('getUser')->willReturn(null);

        $provider = new AdminReceiptListProvider($repo, $security);

        $this->expectException(AccessDeniedHttpException::class);
        $provider->provide(new GetCollection(), []);
    }

    public function testReturnsEmptyWhenUserHasNoDepartment(): void
    {
        $user = $this->createMock(User::class);
        $user->method('getDepartment')->willReturn(null);

        $repo = $this->createMock(ReceiptRepository::class);
        $security = $this->createMock(Security::class);
        $security->method('getUser')->willReturn($user);

        $provider = new AdminReceiptListProvider($repo, $security);
        $result = $provider->provide(new GetCollection(), []);

        $this->assertSame([], $result);
    }
}
