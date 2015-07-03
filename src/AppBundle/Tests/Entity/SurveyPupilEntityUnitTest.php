<?php

namespace AppBundle\Tests\Entity;

use AppBundle\Entity\SurveyPupil;
use AppBundle\Entity\School;

class SurveyPupilEntityUnitTest extends \PHPUnit_Framework_TestCase {

    // Check whether the setName function is working correctly
    public function testSetSchool(){

        $surveyPupil = new SurveyPupil();
        $school = new School();

        $surveyPupil->setSchool($school);

        $this->assertEquals($school, $surveyPupil->getSchool());
    }

    public function testSetQuestion1(){
        $surveyPupil = new SurveyPupil();

        $surveyPupil->setQuestion1(1);

        $this->assertEquals(1, $surveyPupil->getQuestion1());
    }

    public function testSetQuestion2(){
        $surveyPupil = new SurveyPupil();

        $surveyPupil->setQuestion2(1);

        $this->assertEquals(1, $surveyPupil->getQuestion2());
    }

    public function testSetQuestion3(){
        $surveyPupil = new SurveyPupil();

        $surveyPupil->setQuestion3(1);

        $this->assertEquals(1, $surveyPupil->getQuestion3());
    }

    public function testSetQuestion4(){
        $surveyPupil = new SurveyPupil();

        $surveyPupil->setQuestion4(1);

        $this->assertEquals(1, $surveyPupil->getQuestion4());
    }

    public function testSetQuestion5(){
        $surveyPupil = new SurveyPupil();

        $surveyPupil->setQuestion5(1);

        $this->assertEquals(1, $surveyPupil->getQuestion5());
    }

    public function testSetQuestion6(){
        $surveyPupil = new SurveyPupil();

        $surveyPupil->setQuestion6(1);

        $this->assertEquals(1, $surveyPupil->getQuestion6());
    }

    public function testSetQuestion7(){
        $surveyPupil = new SurveyPupil();

        $surveyPupil->setQuestion7(1);

        $this->assertEquals(1, $surveyPupil->getQuestion7());
    }

    public function testSetQuestion8(){
        $surveyPupil = new SurveyPupil();

        $surveyPupil->setQuestion8(1);

        $this->assertEquals(1, $surveyPupil->getQuestion8());
    }

    public function testSetQuestion9(){
        $surveyPupil = new SurveyPupil();

        $surveyPupil->setQuestion9(1);

        $this->assertEquals(1, $surveyPupil->getQuestion9());
    }

}


