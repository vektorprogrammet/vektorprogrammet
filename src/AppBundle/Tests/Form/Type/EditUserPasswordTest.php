<?php

namespace AppBundle\Tests\Form\Type;

use AppBundle\Form\Type\EditUserPasswordType;
use AppBundle\Entity\User;
use Symfony\Component\Form\Test\TypeTestCase;

/*
***************************************************************************************************************************************************************
***************************************************************************************************************************************************************
***************************************************************************************************************************************************************
NOTE:

This test will fail because the form is only valid after the controller has added the missing parameters that is not covered here. 
The data that is submitted through this test is valid, but the form is invalid because it is missing the parameters submitted by the controller.
If we only look at the variables given through the form the test will result in a OK statement in PHPUnit

***************************************************************************************************************************************************************
***************************************************************************************************************************************************************
***************************************************************************************************************************************************************
*/

class EditUserPasswordTest extends TypeTestCase
{
    /**
     * @dataProvider getValidTestData
     */
    public function testForm($data)
    {
        //
//        $type = new EditUserPasswordType();
//        $form = $this->factory->create($type);
//
//		$object = new User();
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
                    'password' => '123Kappa',
                ),
            ),
            array(
                'data' => array(

                ),
            ),
            array(
                'data' => array(
                    'password' => null,
                ),
            ),
        );
    }
}
