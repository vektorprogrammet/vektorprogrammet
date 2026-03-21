<?php

namespace App\Tests\App\Identity\Api\State;

use ApiPlatform\Metadata\Put;
use App\Identity\Api\Resource\ProfileResource;
use App\Identity\Api\State\ProfileProcessor;
use App\Identity\Domain\Events\UserEvent;
use App\Identity\Infrastructure\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class ProfileProcessorTest extends TestCase
{
    public function testProfileUpdateDispatchesUserEditedEvent(): void
    {
        $user = $this->createMock(User::class);
        $user->method('getEmail')->willReturn('old@example.com');
        $user->method('getId')->willReturn(1);
        $user->method('getFirstName')->willReturn('Kari');
        $user->method('getLastName')->willReturn('Nordmann');
        $user->method('getUserName')->willReturn('kari');
        $user->method('getPhone')->willReturn(null);
        $user->method('getGender')->willReturn(null);
        $user->method('getRoles')->willReturn(['ROLE_USER']);
        $user->method('getPicturePath')->willReturn(null);
        $user->method('getFieldOfStudy')->willReturn(null);
        $user->method('getAccountNumber')->willReturn(null);

        $security = $this->createMock(Security::class);
        $security->method('getUser')->willReturn($user);

        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects($this->once())->method('flush');

        $dispatcher = $this->createMock(EventDispatcherInterface::class);
        $dispatcher->expects($this->once())
            ->method('dispatch')
            ->with(
                $this->isInstanceOf(UserEvent::class),
                UserEvent::EDITED
            );

        $processor = new ProfileProcessor($security, $em, $dispatcher);

        $data = new ProfileResource();
        $data->firstName = 'Kari';
        $data->lastName = 'Nordmann';
        $data->email = 'new@example.com';
        $data->phone = null;
        $data->gender = null;

        $result = $processor->process($data, new Put());

        $this->assertInstanceOf(ProfileResource::class, $result);
    }
}
