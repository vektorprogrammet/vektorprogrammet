<?php

namespace Tests\App\Service;

use App\Entity\Department;
use App\Service\GeoLocation;
use App\Service\LogService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class GeoLocationTest extends TestCase
{
    /**
     * @var GeoLocation
     */
    private $geoLocation;
    /**
     * @var Department
     */
    private $dep1;
    /**
     * @var Department
     */
    private $dep2;

    protected function setUp(): void
    {
        parent::setUp();
        $this->dep1 = new Department();
        $this->dep1->setLatitude('63.146057');
        $this->dep1->setLongitude('10.128514');

        $this->dep2 = new Department();
        $this->dep2->setLatitude('63.446057');
        $this->dep2->setLongitude('10.428514');

        $departmentRepo = $this->getMockBuilder(ObjectRepository::class)->getMock();
        $departmentRepo->expects($this->any())
                       ->method('findAll')
                       ->willReturn([$this->dep1, $this->dep2]);

        $entityManager = $this->getMockBuilder(EntityManagerInterface::class)->getMock();
        $entityManager->expects($this->any())
                      ->method('getRepository')
                      ->willReturn($departmentRepo);

        $requestStack = $this->getMockBuilder(RequestStack::class)->getMock();
        $requestStack->expects($this->any())
                     ->method('getMainRequest')
                     ->willReturn(new Request());

        $session = $this->getMockBuilder(SessionInterface::class)->getMock();
        $session->expects($this->any())->method('get')->willReturn(null);
        $session->expects($this->any())->method('set')->willReturn(null);
        $requestStack->expects($this->any())
                     ->method('getSession')
                     ->willReturn($session);

        $logger = $this->getMockBuilder(LogService::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->geoLocation = new GeoLocation('xxxxx', [], $entityManager, $requestStack, $logger);
    }

    public function testDistance()
    {
        $fromLat = '63.416057';
        $fromLon = '10.408514';
        $toLat = '59.666108';
        $toLon = '10.768452';

        $expected = 417389.42572;
        $actual = round($this->geoLocation->distance($fromLat, $fromLon, $toLat, $toLon), 5);

        $this->assertEquals($expected, $actual);
    }

    public function testFindDepartmentClosestTo()
    {
        $coords = [
            'lat' => '63.416057',
            'lon' => '10.408514',
        ];

        $closestDepartment = $this->geoLocation->findDepartmentClosestTo($coords);
        $this->assertEquals($this->dep2, $closestDepartment);
    }
}
