<?php


namespace AppBundle\AutoMapper;

use AppBundle\DataTransferObject\UserDto;
use AppBundle\Entity\User;
use BCC\AutoMapperBundle\Mapper\AbstractMap;

class UserMap extends AbstractMap
{
    /**
     * UserMap constructor.
     */
    public function __construct()
    {
        dump("test");
    }


    /**
     * @return string The source type
     */
    public function getSourceType()
    {
        return User::class;
    }

    /**
     * @return string The destination type
     */
    public function getDestinationType()
    {
        return UserDto::class;
    }
}
