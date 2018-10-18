<?php

namespace AppBundle\Twig\Extension;

use Doctrine\ORM\EntityManagerInterface;

class SponsorsExtension extends \Twig_Extension
{
    protected $doctrine;

    public function __construct(EntityManagerInterface $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function getName()
    {
        return 'SponsorsExtension';
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('get_sponsors', [$this, 'getSponsors']),
            new \Twig_SimpleFunction('get_sponsors_by_size', [$this, 'getSponsorsBySize']),
        );
    }

    public function getSponsors()
    {
        $sponsors = $this->doctrine
            ->getRepository('AppBundle:Sponsor')
            ->findAll();
        if (!$sponsors) {
            return 'No sponsors :-(';
        }

        return $sponsors;
    }

    public function getSponsorsBySize($size)
    {
        $sponsors = $this->doctrine
            ->getRepository('AppBundle:Sponsor')
            ->findBy(array('size' => $size));
        if (!$sponsors) {
            return [];
        }

        return $sponsors;
    }
}
