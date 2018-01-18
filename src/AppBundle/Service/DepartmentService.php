<?php

namespace AppBundle\Service;

use Doctrine\ORM\EntityManager;

class DepartmentService
{
    private $em;
    private $geoLocation;

    public function __construct(EntityManager $em, GeoLocation $geoLocation)
    {
        $this->em = $em;
        $this->geoLocation = $geoLocation;
    }
    
    public function findDepartmentsSortedByGeolocation() {
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
