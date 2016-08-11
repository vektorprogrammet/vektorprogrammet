<?php

namespace AppBundle\Tests\Form\Type;

use AppBundle\Form\Type\ContactType;
use AppBundle\Entity\Contact;
use Symfony\Component\Form\Test\TypeTestCase;

class ContactTypeTest extends TypeTestCase
{
    /**
     * @dataProvider getValidTestData
     */
    public function testForm($data)
    {
        //
//        $type = new ContactType();
//        $form = $this->factory->create($type);
//
//		$object = new Contact();
//
//        $object->fromArray($data);
//
//        // submit the data to the form directly
//        $form->submit($data);
//
//        $this->assertTrue($form->isSynchronized());
//        $this->assertEquals($object, $form->getData());
//
//        $view = $form->createView();
//        $children = $view->children;
//
//        foreach (array_keys($data) as $key) {
//            $this->assertArrayHasKey($key, $children);
//        }
//
    }

    public function getValidTestData()
    {
        return array(
            array(
                'data' => array(
                    'name' => 'Per Olsen',
                    'email' => 'Per@mail.com',
                    'subject' => 'Et subject hehe',
                    'body' => 'A really long text that should fit the entire body of the contact textarea if I can only type enough letters and characters like 123 and ABC',
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
                    'subject' => null,
                    'body' => null,
                ),
            ),
        );
    }
}
