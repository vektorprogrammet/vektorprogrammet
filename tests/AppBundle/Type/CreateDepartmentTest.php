<?php

namespace Tests\AppBundle\Type;

use AppBundle\Form\Type\CreateDepartmentType;
use AppBundle\Entity\Department;
use Symfony\Component\Form\Test\TypeTestCase;

class CreateDepartmentTest extends TypeTestCase
{
    /**
     * @dataProvider getValidTestData
     */
    public function testForm($data)
    {
        $type = CreateDepartmentType::class;
        $form = $this->factory->create($type);

        $object = new Department();

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
                    'name' => 'Universitetet i Østfold',
                    'shortName' => 'UiØ',
                    'email' => 'uiø@mail.com',
                    'address' => 'Ormvegen 12',
                    'active' => false,
                ),
            ),
            array(
                'data' => array('active' => true),
            ),
            array(
                'data' => array(
                    'name' => null,
                    'shortName' => null,
                    'email' => null,
                    'address' => null,
                    'active' => true,
                ),
            ),
        );
    }
}
