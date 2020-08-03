<?php

namespace AppBundle\Twig\Extension;

use AppBundle\Service\GeoLocation;
use Doctrine\ORM\EntityManagerInterface;

class DepartmentExtension extends \Twig_Extension
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
            new \Twig_SimpleFunction('get_departments', array($this, 'getDepartments')),
            new \Twig_SimpleFunction('get_active_departments', array($this, 'getActiveDepartments')),
        );
    }

    public function getDepartments()
    {
        $departments = $this->em->getRepository('AppBundle:Department')->findAll();
        return $this->geoLocationService->sortDepartmentsByDistanceFromClient($departments);
    }

    public function getActiveDepartments()
    {
        $departments = $this->em->getRepository('AppBundle:Department')->findActive();
        return $this->geoLocationService->sortDepartmentsByDistanceFromClient($departments);
    }
}
