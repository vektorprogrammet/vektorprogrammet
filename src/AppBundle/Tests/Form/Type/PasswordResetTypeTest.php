<?php

namespace AppBundle\Tests\Form\Type;

use AppBundle\Entity\PasswordReset;
use AppBundle\Form\Type\PasswordResetType;
use Symfony\Component\Form\Test\TypeTestCase;

class PasswordResetTypeTest extends TypeTestCase {

    /**
     * @dataProvider getValidTestData
     */
    public function testForm($data){

        $type = new PasswordResetType();
        $form = $this->factory->create($type);

        $object = new PasswordReset();

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
                    'username' => 'petjo',
                    'email' => 'petter@stud.ntnu.no',
                ),
            ),
            array(
                'data' => array(

                ),
            ),
            array(
                'data' => array(
                    'name' => null,
                    'email' => null,
                ),
            ),
        );
    }

}