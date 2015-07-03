<?php
/**
 * Created by PhpStorm.
 * User: Tommy
 * Date: 05.05.2015
 * Time: 01:10
 */

namespace AppBundle\Twig\Extension;


class SponsorsExtension extends \Twig_Extension {
    protected $doctrine;

    public function __construct($doctrine){
        $this->doctrine = $doctrine;
    }

    public function getName(){
        return "SponsorsExtension";
    }

    public function getFunctions(){
        return array(
            'get_sponsors' => new \Twig_Function_Method($this, 'getSponsors')
        );
    }

    public function getSponsors(){
        $sponsors = $this->doctrine
            ->getEntityManager()
            ->getRepository('AppBundle:Sponsor')
            ->findAll();
        if (!$sponsors) {
            return "No sponsors :-(";
        }
        return $sponsors;
    }

}