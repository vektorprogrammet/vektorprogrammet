<?php

namespace Tests\App\Type;

use App\Entity\School;
use App\Form\Type\CreateSchoolType;
use Symfony\Component\Form\Test\TypeTestCase;

class CreateSchoolTest extends TypeTestCase
{
    /**
     * @dataProvider getValidTestData
     */
    public function testForm($data)
    {
        $type = CreateSchoolType::class;
        $form = $this->factory->create($type);

        $object = new School();

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
        return [
            [
                'data' => [
                    'name' => 'test',
                    'contactPerson' => 'test2',
                    'phone' => 'test3',
                    'email' => 'test4',
                    'active' => true,
                ],
            ],
            [
                'data' => [
                    'active' => true,
                ],
            ],
            [
                'data' => [
                    'name' => null,
                    'contactPerson' => null,
                    'phone' => null,
                    'email' => null,
                    'active' => true,
                ],
            ],
        ];
    }
}
