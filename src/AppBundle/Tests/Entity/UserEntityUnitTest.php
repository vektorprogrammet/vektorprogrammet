<?php

namespace AppBundle\Tests\Entity;

use AppBundle\Entity\User;
use AppBundle\Entity\Role;
use AppBundle\Entity\FieldOfStudy;

class UserEntityUnitTest extends \PHPUnit_Framework_TestCase
{
    /* Did not manage to get this working 
    // Check whether the setPassword function is working correctly
    public function testSetPassword(){
        
        // new entity
        $user = new User();
        
        // dummy password
        $password = password_hash("test", PASSWORD_BCRYPT, array('cost' => 12));
        
        // Use the setPassword method 
        $user->setPassword("test");
        
        // Assert the result 
        $this->assertEquals($password, $user->getPassword());
        
    }
    */

    // Check whether the setEmail function is working correctly
    public function testSetEmail()
    {

        // new entity
        $user = new User();

        // Use the setEmail method 
        $user->setEmail('per@mail.com');

        // Assert the result 
        $this->assertEquals('per@mail.com', $user->getEmail());
    }

    // Check whether the setIsActive function is working correctly
    public function testSetIsActive()
    {

        // new entity
        $user = new User();

        // Use the setIsActive method 
        $user->setIsActive(1);

        // Assert the result 
        $this->assertEquals(1, $user->getIsActive());
    }

    /*
    // Check whether the setRoles function is working correctly
    public function testSetRoles(){
        
        
        
    }
    */

    // Check whether the setLastName function is working correctly
    public function testSetLastName()
    {

        // new entity
        $user = new User();

        // Use the setLastName method 
        $user->setLastName('olsen');

        // Assert the result 
        $this->assertEquals('olsen', $user->getLastName());
    }

    // Check whether the setFirstname function is working correctly
    public function testSetFirstname()
    {

        // new entity
        $user = new User();

        // Use the setFirstname method 
        $user->setFirstname('olsen');

        // Assert the result 
        $this->assertEquals('olsen', $user->getFirstname());
    }

    // Check whether the setGender function is working correctly
    public function testSetGender()
    {

        // new entity
        $user = new User();

        // Use the setGender method 
        $user->setGender('0');

        // Assert the result 
        $this->assertEquals('0', $user->getGender());
    }

    // Check whether the setPicturePath function is working correctly
    public function testSetPicturePath()
    {

        // new entity
        $user = new User();

        // Use the setPicturePath method 
        $user->setPicturePath('olsen.jpg');

        // Assert the result 
        $this->assertEquals('olsen.jpg', $user->getPicturePath());
    }

    // Check whether the setPhone function is working correctly
    public function testSetPhone()
    {

        // new entity
        $user = new User();

        // Use the setPhone method 
        $user->setPhone('12312312');

        // Assert the result 
        $this->assertEquals('12312312', $user->getPhone());
    }

    // Check whether the setUserName function is working correctly
    public function testSetUserName()
    {

        // new entity
        $user = new User();

        // Use the setUser_name method 
        $user->setUserName('petjo123');

        // Assert the result 
        $this->assertEquals('petjo123', $user->getUserName());
    }

    // Check whether the setFieldOfStudy function is working correctly
    public function testSetFieldOfStudy()
    {

        // new entity
        $user = new User();

        // dummy entity
        $fos = new FieldOfStudy();
        $fos->setName('BIT');

        // Use the setUser_name method 
        $user->setFieldOfStudy($fos);

        // Assert the result 
        $this->assertEquals($fos, $user->getFieldOfStudy());
    }

    // Check whether the addRole function is working correctly
    public function testAddRole()
    {

        // new entity
        $user = new User();

        // New dummy entity 
        $role1 = new Role();
        $role1->setName('role1');

        // Use the addRole method 
        $user->addRole($role1);

        // Roles is stored in an array 
        $roles = $user->getRoles();

        // Loop through the array and check for matches
        foreach ($roles as $role) {
            if ($role1 == $role) {
                // Assert the result
                $this->assertEquals($role1, $role);
            }
        }
    }

    // Check whether the setNewUserCode function is working correctly
    public function testSetNewUserCode()
    {

        // new entity
        $user = new User();

        // Use the setNewUserCode method 
        $user->setNewUserCode('secret');

        // Assert the result 
        $this->assertEquals('secret', $user->getNewUserCode());
    }
}
