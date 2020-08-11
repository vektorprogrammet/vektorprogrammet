<?php

namespace Tests\AppBundle\Service;

use AppBundle\Entity\Department;
use AppBundle\Service\GeoLocation;
use AppBundle\Service\LogService;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\HeaderBag;
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

    protected function setUp()
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
                       ->willReturn([ $this->dep1, $this->dep2 ]);

        $entityManager = $this->getMockBuilder(EntityManagerInterface::class)->getMock();
        $entityManager->expects($this->any())
                      ->method('getRepository')
                      ->willReturn($departmentRepo);

        $sessionStorage = $this->getMockBuilder(SessionInterface::class)->getMock();
        $sessionStorage->expects($this->any())
                       ->method('get')
                       ->willReturn(null);
        $sessionStorage->expects($this->any())
                       ->method('set')
                       ->willReturn(null);


        $requestStack = $this->getMockBuilder(RequestStack::class)->getMock();
        $requestStack->expects($this->any())
                     ->method('getMasterRequest')
                     ->willReturn(new class {
                         public $headers;

                         public function __construct()
                         {
                             $this->headers = new HeaderBag();
                         }
                     });

        $logger = $this->getMockBuilder(LogService::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->geoLocation = new GeoLocation('xxxxx', [], $entityManager, $sessionStorage, $requestStack, $logger);
    }

    public function testDistance()
    {
        $fromLat = '63.416057';
        $fromLon = '10.408514';
        $toLat   = '59.666108';
        $toLon   = '10.768452';

        $expected = 417389.42572;
        $actual   = round($this->geoLocation->distance($fromLat, $fromLon, $toLat, $toLon), 5);

        $this->assertEquals($expected, $actual);
    }

    public function testFindDepartmentClosestTo()
    {
        $coords = [
            'lat' => '63.416057',
            'lon' => '10.408514'
        ];

        $closestDepartment = $this->geoLocation->findDepartmentClosestTo($coords);
        $this->assertEquals($this->dep2, $closestDepartment);
    }
}
