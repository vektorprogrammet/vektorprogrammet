<?php

namespace AppBundle\Twig\Extension;

use AppBundle\Entity\Department;
use Doctrine\ORM\EntityManagerInterface;

class SemesterExtension extends \Twig_Extension
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function getName()
    {
        return 'semester_extension';
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('get_semesters', array($this, 'getSemesters')),
        );
    }

    public function getSemesters()
    {
        return $this->em->getRepository('AppBundle:Semester')->findAllOrderedByAge();
    }
}
