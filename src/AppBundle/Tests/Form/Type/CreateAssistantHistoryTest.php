<?php

namespace AppBundle\Tests\Form\Type;

use AppBundle\Form\Type\CreateAssistantHistoryType;
use AppBundle\Entity\AssistantHistory;
use AppBundle\Entity\Department;
use Symfony\Component\Form\Test\TypeTestCase;

class CreateAssistantHistoryTest extends TypeTestCase {
	
	/**
     * @dataProvider getValidTestData
     */
	public function testForm($data){
	
		 // First, mock the object to be used in the test
        $department= $this->getMock('\AppBundle\Entity\department');
		
		// Now mock the repository
        $departmentRepository = $this
            ->getMockBuilder('\Doctrine\ORM\EntityRepository')
            ->disableOriginalConstructor()
            ->getMock();
		
		$entityManager = $this
            ->getMockBuilder('\Doctrine\Common\Persistence\ObjectManager')
            ->disableOriginalConstructor()
            ->getMock();
        $entityManager->expects($this->once())
            ->method('getRepository')
            ->will($this->returnValue($departmentRepository));

	
        $type = new CreateAssistantHistoryType($department);
        $form = $this->factory->create($type);
		
		$object = new AssistantHistory();
		
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
                    'semester' => 'test',
					'workdays' => 'test2',
					'school' => 'test3',
                ),
            ),
            array(
                'data' => array(
				
				),
            ),
            array(
                'data' => array(
                    'semester' => null,
					'workdays' => null,
					'school' => null,
                ),
            ),
        );
    }
	
}
       