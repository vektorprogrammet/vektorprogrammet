<?php

namespace App\Twig\Extension;

use App\Shared\Entity\Semester;
use Doctrine\ORM\EntityManagerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class SemesterExtension extends AbstractExtension
{
    public function __construct(private readonly EntityManagerInterface $em)
    {
    }

    public function getName()
    {
        return 'semester_extension';
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('get_semesters', $this->getSemesters(...)),
        ];
    }

    public function getSemesters()
    {
        return $this->em->getRepository(Semester::class)->findAllOrderedByAge();
    }
}
