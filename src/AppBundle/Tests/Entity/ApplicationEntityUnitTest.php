<?php

namespace AppBundle\Tests\Entity;

use AppBundle\Entity\Application;
use AppBundle\Entity\Interview;
use AppBundle\Entity\ApplicationStatistic;

class ApplicationEntityUnitTest extends \PHPUnit_Framework_TestCase {

    public function testSetFirstName(){

        $application = new Application();

        $application->setFirstName("Jan");

        $this->assertEquals("Jan", $application->getFirstName());

    }

    public function testSetLastName(){

        $application = new Application();

        $application->setLastName("Johansen");

        $this->assertEquals("Johansen", $application->getLastName());

    }

    public function testSetPhone(){

        $application = new Application();

        $application->setPhone("95999999");

        $this->assertEquals("95999999", $application->getPhone());

    }

    public function testSetEmail(){

        $application = new Application();

        $application->setEmail("jon@gmail.com");

        $this->assertEquals("jon@gmail.com", $application->getEmail());

    }

    public function testSetUserCreated(){

        $application = new Application();

        $application->setUserCreated(true);

        $this->assertEquals(true, $application->getUserCreated());

    }

    public function testSetStatistic(){

        $application = new Application();

        $applicationStatistic = new ApplicationStatistic();

        $application->setStatistic($applicationStatistic);

        $this->assertEquals($applicationStatistic, $application->getStatistic());

    }

    public function testSetInterview(){

        $application = new Application();

        $interview = new Interview();

        $application->setInterview($interview);

        $this->assertEquals($interview, $application->getInterview());

    }

    public function testIsSubstituteCreated(){

        $application = new Application();

        $application->setSubstituteCreated(true);

        $this->assertEquals(true, $application->getSubstituteCreated());

    }

}