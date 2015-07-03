<?php

namespace AppBundle\Tests\Form\Type;

use AppBundle\Form\Type\CreateForumType;
use AppBundle\Entity\Forum;
use Symfony\Component\Form\Test\TypeTestCase;

class CreateForumTest extends TypeTestCase {
	
	/**
     * @dataProvider getValidTestData
     */
	public function testForm($data){
	
        $type = new CreateForumType();
        $form = $this->factory->create($type);
		
		$object = new Forum();
		
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
                    'name' => 'Spørsmål',
					'description' => 'Her kan du stille spørsmål.',
					'type' => 'team',
                ),
            ),
            array(
                'data' => array(
				
				),
            ),
            array(
                'data' => array(
                    'name' => null,
					'description' => null,
					'type' => null,
                ),
            ),
        );
    }
	
}
       