<?php

namespace AppBundle\Tests\Form\Type;

use AppBundle\Form\Type\CreateSchoolType;
use AppBundle\Entity\School;
use Symfony\Component\Form\Test\TypeTestCase;

class CreateSchoolTest extends TypeTestCase {
	
	/**
     * @dataProvider getValidTestData
     */
	public function testForm($data){
	
        $type = new CreateSchoolType();
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
        return array(
            array(
                'data' => array(
                    'name' => 'test',
					'contactPerson' => 'test2',
					'phone' => 'test3',
					'email' => 'test4',
                ),
            ),
            array(
                'data' => array(
				
				),
            ),
            array(
                'data' => array(
                    'name' => null,
					'contactPerson' => null,
					'phone' => null,
					'email' => null,
                ),
            ),
        );
    }
	
}
       