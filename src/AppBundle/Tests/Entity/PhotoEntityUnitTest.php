<?php

namespace AppBundle\Tests\Entity;

use AppBundle\Entity\Photo;
use AppBundle\Entity\User;

class PhotoEntityUnitTest extends \PHPUnit_Framework_TestCase
{
    public function testSetDateAdded()
    {
        $photo = new Photo();
        $date = new \DateTime();

        $photo->setDateAdded($date);

        $this->assertEquals($date, $photo->getDateAdded());
    }

    public function testSetDateTaken()
    {
        $photo = new Photo();
        $date = new \DateTime();

        $photo->setDateTaken($date);

        $this->assertEquals($date, $photo->getDateTaken());
    }

    public function testSetAddedByUser()
    {
        $photo = new Photo();
        $user = new User();

        $photo->setAddedByUser($user);

        $this->assertEquals($user, $photo->getAddedByUser());
    }

    public function testSetComment()
    {
        $photo = new Photo();

        $photo->setComment('This is a photo comment');

        $this->assertEquals('This is a photo comment', $photo->getComment());
    }

    public function testSetPathToFile()
    {
        $photo = new Photo();

        $photo->setPathToFile('/pictures/picture');

        $this->assertEquals('/pictures/picture', $photo->getPathToFile());
    }

    public function testSetGallery()
    {
        $photo = new Photo();
        $gallery = new \AppBundle\Entity\Gallery();

        $photo->setGallery($gallery);

        $this->assertEquals($gallery, $photo->getGallery());
    }
}
