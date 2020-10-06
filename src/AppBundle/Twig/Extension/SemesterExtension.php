<?php

namespace AppBundle\Twig\Extension;

use Doctrine\ORM\EntityManagerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class SemesterExtension extends AbstractExtension
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
            new TwigFunction('get_semesters', array($this, 'getSemesters')),
        );
    }

    public function getSemesters()
    {
        return $this->em->getRepository('AppBundle:Semester')->findAllOrderedByAge();
    }
}
