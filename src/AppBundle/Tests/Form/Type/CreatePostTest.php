<?php

namespace AppBundle\Tests\Form\Type;

use AppBundle\Form\Type\CreatePostType;
use AppBundle\Entity\Post;
use Symfony\Component\Form\Test\TypeTestCase;

class CreatePostTest extends TypeTestCase
{
    /**
     * @dataProvider getValidTestData
     */
    public function testForm($data)
    {
        $type = new CreatePostType();
        $form = $this->factory->create($type);

        $object = new Post();

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
                    'Subject' => 'A subject.',
                    'text' => 'A long text.......',
                ),
            ),
            array(
                'data' => array(

                ),
            ),
            array(
                'data' => array(
                    'Subject' => null,
                    'text' => null,
                ),
            ),
        );
    }
}
