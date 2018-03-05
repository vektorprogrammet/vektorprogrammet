<?php

namespace AppBundle\Twig\Extension;

use AppBundle\Service\GeoLocation;
use Doctrine\ORM\EntityManager;

class DepartmentExtension extends \Twig_Extension
{
    private $em;
    private $geoLocationService;

    public function __construct(GeoLocation $geoLocationService, EntityManager $em)
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
        );
    }

    public function getDepartments()
    {
        $departments = $this->em->getRepository('AppBundle:Department')->findAll();
        return $this->geoLocationService->sortDepartmentsByDistanceFromClient($departments);
    }
}
