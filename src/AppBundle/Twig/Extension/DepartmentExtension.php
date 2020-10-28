<?php

namespace AppBundle\Twig\Extension;

use AppBundle\Entity\Department;
use AppBundle\Service\GeoLocation;
use Doctrine\ORM\EntityManagerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class DepartmentExtension extends AbstractExtension
{
    private $em;
    private $geoLocationService;

    public function __construct(GeoLocation $geoLocationService, EntityManagerInterface $em)
    {
        $this->geoLocationService = $geoLocationService;
        $this->em = $em;
    }

    public function getName()
    {
        return 'department_extension';
    }

    public function getFunctions()
    {
        return array(
            new TwigFunction('get_departments', array($this, 'getDepartments')),
            new TwigFunction('get_active_departments', array($this, 'getActiveDepartments')),
        );
    }

    public function getDepartments()
    {
        $departments = $this->em->getRepository(Department::class)->findAll();
        return $this->geoLocationService->sortDepartmentsByDistanceFromClient($departments);
    }

    public function getActiveDepartments()
    {
        $departments = $this->em->getRepository(Department::class)->findActive();
        return $this->geoLocationService->sortDepartmentsByDistanceFromClient($departments);
    }
}
