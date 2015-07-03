<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\StaticContent;


class LoadStatic_contentData extends AbstractFixture{
    public function load(ObjectManager $manager){
        $elements = array(
            'infobox_one' => 'Vektorprogrammet er et studentdrevet tiltak i Trondheimsregionen, Akershus og Oslo, som skal bidra til å øke interessen og forståelsen for matematikk i ungdomsskolen.
            Dette gjøres ved å sende studenter fra realfagssterke linjer på NTNU, NMBU og UiO ut til ungdomsskolen der de bidrar med sin kunnskap i timene.',
            'infobox_two' => 'Vektorprogrammet er et studentdrevet tiltak i Trondheimsregionen, Akershus og Oslo, som skal bidra til å øke interessen og forståelsen for matematikk i',
            'banner_one' => 'Vektorprogrammet bidrar til å øke interessen og forståelsen for matematikk i ungdomsskolen',
            'banner_two' => 'banner_two',
            'vektor_for_studenter' => 'Vektor for studenter',
            'vektor_for_bedrifter' => 'Vektor for bedrifter',
            'vektor_for_laerere' => 'Vektor for lærere',
            'vektor_i_media' => '<p>Vektorprogrammet i media:</p><p>VG: Studenter hjelper elever med matte</p><p>NRK: Studenter hjelper elever med matte</p><p>DAGBLADET: Studenter hjelper elever med matte</p><p>AFTENPOSTEN: Studenter hjelper elever med matte</p>'
        );
        foreach ($elements as $html_id => $content) {
            $staticElement = new StaticContent();
            $staticElement->setHtmlId($html_id);
            $staticElement->setHtml($content);

            $manager->persist($staticElement);
            echo("persisted " . $staticElement->getHtmlId()
                        . "  "  .$staticElement->getHtml()
                    . "id: " . $staticElement->getId() . "\r\n");
        }
        $manager->flush();
    }
}