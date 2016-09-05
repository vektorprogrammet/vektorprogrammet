<?php

namespace AppBundle\Tests\Form\Type;

use AppBundle\Form\Type\CreateTeamType;
use AppBundle\Entity\Team;
use Symfony\Component\Form\Test\TypeTestCase;

class CreateTeamTest extends TypeTestCase
{
    /**
     * @dataProvider getValidTestData
     */
    public function testForm($data)
    {
        /*$type = new CreateTeamType();
        $form = $this->factory->create($type);

        $object = new Team();

        $object->fromArray($data);

        // submit the data to the form directly
        $form->submit($data);

        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($object, $form->getData());

        $view = $form->createView();
        $children = $view->children;

        foreach (array_keys($data) as $key) {
            $this->assertArrayHasKey($key, $children);
        }*/
    }

    public function getValidTestData()
    {
        return array(
            array(
                'data' => array(
                    'name' => 'test',
                ),
            ),
            array(
                'data' => array(

                ),
            ),
            array(
                'data' => array(
                    'name' => null,
                ),
            ),
        );
    }
}
