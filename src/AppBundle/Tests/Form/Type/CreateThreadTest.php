<?php

namespace AppBundle\Tests\Form\Type;

use AppBundle\Form\Type\CreateThreadType;
use AppBundle\Entity\Thread;
use Symfony\Component\Form\Test\TypeTestCase;

class CreateThreadTest extends TypeTestCase
{
    /**
     * @dataProvider getValidTestData
     */
    public function testForm($data)
    {
        $type = new CreateThreadType();
        $form = $this->factory->create($type);

        $object = new Thread();

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
                    'subject' => 'Dis be a subject',
                    'text' => 'Dis be a text',
                ),
            ),
            array(
                'data' => array(

                ),
            ),
            array(
                'data' => array(
                    'subject' => null,
                    'text' => null,
                ),
            ),
        );
    }
}
