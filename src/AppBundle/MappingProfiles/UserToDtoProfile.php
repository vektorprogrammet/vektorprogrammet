<?php
namespace AppBundle\MappingProfiles;

use AppBundle\DataTransferObject\UserDto;
use AppBundle\Entity\User;
use BCC\AutoMapperBundle\Mapper\AbstractMap;

class UserToDtoProfile extends AbstractMap {

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


    public function __construct()
    {
        $this->buildDefaultMap();
    }
}