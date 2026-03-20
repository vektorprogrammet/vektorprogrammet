<?php

namespace Tests\App\Type;

use App\Organization\Infrastructure\Entity\Position;
use App\Organization\Form\CreatePositionType;
use Symfony\Component\Form\Test\TypeTestCase;

class CreatePositionTest extends TypeTestCase
{
    /**
     * @dataProvider getValidTestData
     */
    public function testForm($data)
    {
        $type = CreatePositionType::class;
        $form = $this->factory->create($type);

        $object = new Position();

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
                    'name' => 'Leder',
                ],
            ],
            [
                'data' => [],
            ],
            [
                'data' => [
                    'name' => null,
                ],
            ],
        ];
    }
}
