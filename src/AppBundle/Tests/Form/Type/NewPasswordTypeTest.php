<?php

namespace AppBundle\Tests\Form\Type;

use AppBundle\Entity\User;
use AppBundle\Form\Type\NewPasswordType;
use Symfony\Component\Form\Test\TypeTestCase;

class NewPasswordTypeTest extends TypeTestCase {

    /**
     * @dataProvider getValidTestData
     */
    public function testForm($data){

        $type = new NewPasswordType();
        $form = $this->factory->create($type);

        $object = new User();

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
        return array(
            array(
                'data' => array(
                    'password' => 'thisisaverylongpassordwithover30characters',
                ),
            ),
            array(
                'data' => array(

                ),
            ),
            array(
                'data' => array(
                    'password' => null
                ),
            ),
        );
    }

}