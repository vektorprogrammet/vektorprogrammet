<?php

namespace AllocationBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class SchoolAllocationController extends Controller
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("/kontrollpanel/skole/fordeling/allokering", name="school_allocation_allocationMain")
     */
    public function indexAction()
    {
        return $this->render('@Allocation/index.html.twig');
    }
}
