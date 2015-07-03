<?php

namespace AppBundle\Tests\Form\Type;

use AppBundle\Entity\School;
use AppBundle\Entity\SurveyTeacher;
use AppBundle\Form\Type\SurveyTeacherType;
use Symfony\Component\Form\Test\TypeTestCase;

class SurveyTeacherTypeTest extends TypeTestCase {

    /**
     * @dataProvider getValidTestData
     */
    public function testForm($data){

        $type = new SurveyTeacherType();
        $form = $this->factory->create($type);

        $object = new SurveyTeacher();

        $object->fromArray($data);

        // submit the data to the form directly
        $form->submit($data);

        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($object, $form->getData());

        $view = $form->createView();
        $children = $view->children;

        foreach (array_keys($data) as $key) {
            $this->assertArrayHasKey($key, $children);
        }

    }

    public function getValidTestData()
    {
        $school = new School();
        return array(
            array(
                'data' => array(
                    'school' => $school,
                    'question1' => 1,
                    'question2' => 2,
                    'question3' => 1,
                    'question4' => 1,
                    'question5' => 1,
                    'question6' => 1,
                    'question7' => 1,
                    'question8' => 1,
                    'question10' => 'This is a textual feedback for vektorprogrammet.',
                ),
            ),
            array(
                'data' => array(

                ),
            ),
            array(
                'data' => array(
                    'school' => null,
                    'question1' => null,
                    'question2' => null,
                    'question3' => null,
                    'question4' => null,
                    'question5' => null,
                    'question6' => null,
                    'question7' => null,
                    'question8' => null,
                    'question9' => null,
                ),
            ),
        );
    }

}