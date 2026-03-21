<?php

namespace App\Tests\App\Identity\Api\State;

use ApiPlatform\Metadata\Get;
use App\Identity\Api\Resource\ProfileResource;
use App\Identity\Api\State\ProfileProvider;
use App\Identity\Infrastructure\Entity\User;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\SecurityBundle\Security;

class ProfileProviderMappingTest extends TestCase
{
    public function testProviderMapsAccountNumber(): void
    {
        $user = $this->createMock(User::class);
        $user->method('getId')->willReturn(42);
        $user->method('getFirstName')->willReturn('Kari');
        $user->method('getLastName')->willReturn('Nordmann');
        $user->method('getUserName')->willReturn('karinord');
        $user->method('getEmail')->willReturn('kari@example.com');
        $user->method('getPhone')->willReturn(null);
        $user->method('getGender')->willReturn(null);
        $user->method('getAccountNumber')->willReturn('1234.56.78901');
        $user->method('getFieldOfStudy')->willReturn(null);
        $user->method('getPicturePath')->willReturn(null);
        $user->method('getRoles')->willReturn(['ROLE_USER']);

        $security = $this->createMock(Security::class);
        $security->method('getUser')->willReturn($user);

        $provider = new ProfileProvider($security);
        /** @var ProfileResource $resource */
        $resource = $provider->provide(new Get(), []);

        $this->assertSame('1234.56.78901', $resource->accountNumber);
    }
}
