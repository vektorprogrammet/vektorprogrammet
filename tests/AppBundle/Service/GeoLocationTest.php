<?php

namespace Tests\AppBundle\Service;

use AppBundle\Entity\Department;
use AppBundle\Service\GeoLocation;
use AppBundle\Service\LogService;
use Doctrine\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\RequestStack;

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

        $departmentRepo = $this->createStub(ObjectRepository::class);
        $departmentRepo->method('findAll')
                       ->willReturn([ $this->dep1, $this->dep2 ]);

        $entityManager = $this->createStub(EntityManagerInterface::class);
        $entityManager->method('getRepository')
                      ->willReturn($departmentRepo);

        $request = \Symfony\Component\HttpFoundation\Request::create('/');
        $request->setSession(new \Symfony\Component\HttpFoundation\Session\Session(new \Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage()));
        $requestStack = new RequestStack();
        $requestStack->push($request);

        $logger = $this->createStub(LogService::class);

        $this->geoLocation = new GeoLocation('xxxxx', [], $entityManager, $requestStack, $logger);
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
