<?php

namespace App\AutoMapper;

use App\DataTransferObject\UserDto;
use App\Entity\User;
use BCC\AutoMapperBundle\Mapper\AbstractMap;

class UserMap extends AbstractMap
{
    /**
     * UserMap constructor.
     */
    public function __construct()
    {
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
