<?php

namespace AppBundle\Tests\Form\Type;

use AppBundle\Form\Type\InterviewPracticalType;
use AppBundle\Entity\InterviewPractical;
use Symfony\Component\Form\Test\TypeTestCase;

class InterviewPracticalTest extends TypeTestCase
{
    /**
     * @dataProvider getValidTestData
     */
    public function testForm($data)
    {
        //
//        $type = new InterviewPracticalType();
//        $form = $this->factory->create($type);
//
//		$object = new InterviewPractical();
//
//        $object->fromArray($data);
//
//        // submit the data to the form directly
//        $form->submit($data);
//
//        $this->assertTrue($form->isSynchronized());
//        $this->assertEquals($object, $form->getData());
//
//        $view = $form->createView();
//        $children = $view->children;
//
//        foreach (array_keys($data) as $key) {
//            $this->assertArrayHasKey($key, $children);
//        }
//
    }

    public function getValidTestData()
    {
        return array(
            array(
                'data' => array(
                    'position' => '2 x 4',
                    'monday' => 'bra',
                    'tuesday' => 'bra',
                    'wednesday' => 'Ikke',
                    'thursday' => 'Ikke',
                    'friday' => 'Ikke',
                    'substitute' => 0,
                    'english' => 0,
                    'heardAboutFrom' => 0,
                ),
            ),
            array(
                'data' => array(

                ),
            ),
            array(
                'data' => array(
                    'position' => null,
                    'monday' => null,
                    'tuesday' => null,
                    'wednesday' => null,
                    'thursday' => null,
                    'friday' => null,
                    'substitute' => null,
                    'english' => null,
                    'heardAboutFrom' => null,
                ),
            ),
        );
    }
}
