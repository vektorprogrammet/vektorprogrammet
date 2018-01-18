<?php

namespace AppBundle\Twig\Extension;

use AppBundle\Service\DepartmentService;

class DepartmentExtension extends \Twig_Extension
{
    private $departmentService;

    public function __construct(DepartmentService $departmentService)
    {
        $this->departmentService = $departmentService;
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
        return $this->departmentService->findDepartmentsSortedByGeolocation();
    }
}
