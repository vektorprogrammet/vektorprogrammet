<?php

namespace AppBundle\Tests\Entity;

use AppBundle\Entity\Image;
use AppBundle\Entity\ImageGallery;
use Doctrine\Common\Collections\ArrayCollection;

class ImageEntityUnitTest extends \PHPUnit_Framework_TestCase
{
    public function testSetGalleries()
    {
        $imageGalleries = new ArrayCollection([new ImageGallery(), new ImageGallery()]);
        $image = new Image();
        $image->setGalleries($imageGalleries);

        $this->assertEquals($imageGalleries, $image->getGalleries());
    }

    public function testSetPath()
    {
        $image = new Image();
        $path = 'images/lovely_image.png';
        $image->setPath($path);

        $this->assertEquals($path, $image->getPath());
    }

    public function testSetDescription()
    {
        $image = new Image();
        $description = 'Tor får servert kake';
        $image->setDescription($description);

        $this->assertEquals($description, $image->getDescription());
    }
}
