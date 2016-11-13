<?php

namespace AppBundle\Twig\Extension;

class SponsorsExtension extends \Twig_Extension
{
    protected $doctrine;

    public function __construct($doctrine)
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
            'get_sponsors' => new \Twig_Function_Method($this, 'getSponsors'),
            'get_sponsors_by_size' => new \Twig_Function_method($this, 'getSponsorsBySize'),
        );
    }

    public function getSponsors()
    {
        $sponsors = $this->doctrine
            ->getEntityManager()
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
            ->getEntityManager()
            ->getRepository('AppBundle:Sponsor')
            ->findBy(array('size' => $size));
        if (!$sponsors) {
            return [];
        }

        return $sponsors;
    }
}
