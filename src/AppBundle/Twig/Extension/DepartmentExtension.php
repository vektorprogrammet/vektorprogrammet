<?php

namespace AppBundle\Twig\Extension;

use AppBundle\Service\GeoLocation;
use Doctrine\ORM\EntityManager;

class DepartmentExtension extends \Twig_Extension
{
    private $em;
    private $geoLocation;

    public function __construct(EntityManager $em, GeoLocation $geoLocation)
    {
        $this->em = $em;
        $this->geoLocation = $geoLocation;
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
        $closestDepartment = $this->geoLocation->findNearestDepartment();
        if (!$closestDepartment) {
            return $departments;
        }

        $sortedDepartments = [$closestDepartment];
        foreach ($departments as $department) {
            if ($department !== $closestDepartment) {
                $sortedDepartments[] = $department;
            }
        }

        return $sortedDepartments;
    }
}
