<?php

namespace AppBundle\Tests\Entity;

use AppBundle\Entity\FieldOfStudy;
use AppBundle\Entity\Interview;
use AppBundle\Entity\InterviewSchema;
use AppBundle\Entity\Semester;
use AppBundle\Entity\Substitute;


class SubstituteEntityUnitTest extends \PHPUnit_Framework_TestCase
{

    public function testSetFirstName()
    {

        $substitute = new Substitute();

        $substitute->setFirstName("Jan");

        $this->assertEquals("Jan", $substitute->getFirstName());

    }

    public function testSetLastName()
    {

        $substitute = new Substitute();

        $substitute->setLastName("Johansen");

        $this->assertEquals("Johansen", $substitute->getLastName());

    }

    public function testSetPhone()
    {

        $substitute = new Substitute();

        $substitute->setPhone("95999999");

        $this->assertEquals("95999999", $substitute->getPhone());

    }

    public function testSetEmail()
    {

        $substitute = new Substitute();

        $substitute->setEmail("jan@gmail.com");

        $this->assertEquals("jan@gmail.com", $substitute->getEmail());

    }

    public function testSetYearOfStudy()
    {

        $substitute = new Substitute();

        $substitute->setYearOfStudy(1);

        $this->assertEquals(1, $substitute->getYearOfStudy());

    }

    public function testSetMonday()
    {

        $substitute = new Substitute();

        $substitute->setMonday("Bra");

        $this->assertEquals("Bra", $substitute->getMonday());

    }

    public function testSetTuesday()
    {

        $substitute = new Substitute();

        $substitute->setTuesday("Bra");

        $this->assertEquals("Bra", $substitute->getTuesday());

    }

    public function testSetWednesday()
    {

        $substitute = new Substitute();

        $substitute->setWednesday("Bra");

        $this->assertEquals("Bra", $substitute->getWednesday());

    }

    public function testSetThursday()
    {

        $substitute = new Substitute();

        $substitute->setThursday("Bra");

        $this->assertEquals("Bra", $substitute->getThursday());

    }

    public function testSetFriday()
    {

        $substitute = new Substitute();

        $substitute->setFriday("Bra");

        $this->assertEquals("Bra", $substitute->getFriday());

    }

    public function testSetFieldOfStudy()
    {

        $substitute = new Substitute();
        $fos = new FieldOfStudy();

        $substitute->setFieldOfStudy($fos);

        $this->assertEquals($fos, $substitute->getFieldOfStudy());

    }

    public function testSetSemester()
    {

        $substitute = new Substitute();
        $semester = new Semester();

        $substitute->setSemester($semester);

        $this->assertEquals($semester, $substitute->getSemester());

    }

}