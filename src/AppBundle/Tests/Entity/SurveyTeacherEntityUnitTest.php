<?php

namespace AppBundle\Tests\Entity;

use AppBundle\Entity\SurveyTeacher;
use AppBundle\Entity\School;

class SurveyTeacherEntityUnitTest extends \PHPUnit_Framework_TestCase {

    // Check whether the setName function is working correctly
    public function testSetSchool(){

        $surveyTeacher = new SurveyTeacher();
        $school = new School();

        $surveyTeacher->setSchool($school);

        $this->assertEquals($school, $surveyTeacher->getSchool());
    }

    public function testSetQuestion1(){
        $surveyTeacher = new SurveyTeacher();

        $surveyTeacher->setQuestion1(1);

        $this->assertEquals(1, $surveyTeacher->getQuestion1());
    }

    public function testSetQuestion2(){
        $surveyTeacher = new SurveyTeacher();

        $surveyTeacher->setQuestion2(1);

        $this->assertEquals(1, $surveyTeacher->getQuestion2());
    }

    public function testSetQuestion3(){
        $surveyTeacher = new SurveyTeacher();

        $surveyTeacher->setQuestion3(1);

        $this->assertEquals(1, $surveyTeacher->getQuestion3());
    }

    public function testSetQuestion4(){
        $surveyTeacher = new SurveyTeacher();

        $surveyTeacher->setQuestion4(1);

        $this->assertEquals(1, $surveyTeacher->getQuestion4());
    }

    public function testSetQuestion5(){
        $surveyTeacher = new SurveyTeacher();

        $surveyTeacher->setQuestion5(1);

        $this->assertEquals(1, $surveyTeacher->getQuestion5());
    }

    public function testSetQuestion6(){
        $surveyTeacher = new SurveyTeacher();

        $surveyTeacher->setQuestion6(1);

        $this->assertEquals(1, $surveyTeacher->getQuestion6());
    }

    public function testSetQuestion7(){
        $surveyTeacher = new SurveyTeacher();

        $surveyTeacher->setQuestion7(1);

        $this->assertEquals(1, $surveyTeacher->getQuestion7());
    }

    public function testSetQuestion8(){
        $surveyTeacher = new SurveyTeacher();

        $surveyTeacher->setQuestion8(1);

        $this->assertEquals(1, $surveyTeacher->getQuestion8());
    }

    public function testSetQuestion9(){
        $surveyTeacher = new SurveyTeacher();

        $surveyTeacher->setQuestion9(1);

        $this->assertEquals(1, $surveyTeacher->getQuestion9());
    }

}


