<?php

declare(strict_types=1);

namespace App\Tests\App\Operations\Api\State;

use ApiPlatform\Metadata\GetCollection;
use App\Identity\Infrastructure\Entity\User;
use App\Operations\Api\Resource\UserReceiptListResource;
use App\Operations\Api\State\UserReceiptListProvider;
use App\Operations\Infrastructure\Entity\Receipt;
use App\Operations\Infrastructure\Repository\ReceiptRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class UserReceiptListProviderTest extends TestCase
{
    public function testReturnsCurrentUserReceipts(): void
    {
        $user = $this->createMock(User::class);

        $receipt = $this->createMock(Receipt::class);
        $receipt->method('getId')->willReturn(1);
        $receipt->method('getVisualId')->willReturn('abc123');
        $receipt->method('getDescription')->willReturn('Bus ticket');
        $receipt->method('getSum')->willReturn(150.0);
        $receipt->method('getReceiptDate')->willReturn(new \DateTime('2026-01-10'));
        $receipt->method('getSubmitDate')->willReturn(new \DateTime('2026-01-10'));
        $receipt->method('getStatus')->willReturn(Receipt::STATUS_PENDING);
        $receipt->method('getRefundDate')->willReturn(null);

        $repo = $this->createMock(ReceiptRepository::class);
        $repo->expects($this->once())
            ->method('findByUserOrdered')
            ->with($user, null)
            ->willReturn([$receipt]);

        $security = $this->createMock(Security::class);
        $security->method('getUser')->willReturn($user);

        $provider = new UserReceiptListProvider($repo, $security);
        $result = $provider->provide(new GetCollection(), []);

        $this->assertCount(1, $result);
        $this->assertInstanceOf(UserReceiptListResource::class, $result[0]);
        $this->assertSame(1, $result[0]->id);
        $this->assertSame('abc123', $result[0]->visualId);
        $this->assertSame(Receipt::STATUS_PENDING, $result[0]->status);
        $this->assertNull($result[0]->refundDate);
    }

    public function testPassesStatusFilterToRepository(): void
    {
        $user = $this->createMock(User::class);

        $repo = $this->createMock(ReceiptRepository::class);
        $repo->expects($this->once())
            ->method('findByUserOrdered')
            ->with($user, 'pending')
            ->willReturn([]);

        $security = $this->createMock(Security::class);
        $security->method('getUser')->willReturn($user);

        $provider = new UserReceiptListProvider($repo, $security);
        $result = $provider->provide(new GetCollection(), [], ['filters' => ['status' => 'pending']]);

        $this->assertSame([], $result);
    }

    public function testThrowsWhenNotAuthenticated(): void
    {
        $repo = $this->createMock(ReceiptRepository::class);
        $security = $this->createMock(Security::class);
        $security->method('getUser')->willReturn(null);

        $provider = new UserReceiptListProvider($repo, $security);

        $this->expectException(AccessDeniedHttpException::class);
        $provider->provide(new GetCollection(), []);
    }

    public function testOtherUsersReceiptsNotIncluded(): void
    {
        $user = $this->createMock(User::class);

        // Repository is user-scoped — if it returns empty, no other user's receipts appear
        $repo = $this->createMock(ReceiptRepository::class);
        $repo->method('findByUserOrdered')->with($user, null)->willReturn([]);

        $security = $this->createMock(Security::class);
        $security->method('getUser')->willReturn($user);

        $provider = new UserReceiptListProvider($repo, $security);
        $result = $provider->provide(new GetCollection(), []);

        $this->assertSame([], $result);
    }
}
