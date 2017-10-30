<?php

namespace AppBundle\Tests\Entity;

use AppBundle\Entity\Image;
use AppBundle\Entity\ImageGallery;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImageEntityUnitTest extends \PHPUnit_Framework_TestCase
{
    public function testSetGallery()
    {
        $imageGallery = new ImageGallery();
        $image = new Image();
        $image->setGallery($imageGallery);

        $this->assertEquals($imageGallery, $image->getGallery());
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

    public function testSetUploadedFile()
    {
        $image = new Image();
        $mockUploadedFile = new UploadedFile(tempnam(sys_get_temp_dir(), ''), 'mockfile');
        $image->setUploadedFile($mockUploadedFile);

        $this->assertEquals($mockUploadedFile, $image->getUploadedFile());
    }

    public function testSetFilters()
    {
        $image = new Image();
        $filters = array('max_500px', 'square_300px');
        $image->setFilters($filters);

        $this->assertEquals($filters, $image->getFilters());
    }

    public function testAddFilter()
    {
        $image = new Image();
        $filter = 'max_500px';
        $image->addFilter($filter);

        $this->assertEquals($filter, $image->getFilters()[0]);
    }

    public function testSetResolutions()
    {
        $image = new Image();
        $resolutions = array(
            array(
                'x' => 400,
                'y' => 300,
                'path' => 'fakepath',
            ),
            array(
                'x' => 200,
                'y' => 400,
                'path' => 'fakepath',
            ),
        );
        $image->setResolutions($resolutions);

        $this->assertEquals($resolutions, $image->getResolutions());
    }
}
