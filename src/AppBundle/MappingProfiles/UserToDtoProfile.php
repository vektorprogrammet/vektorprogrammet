<?php
namespace AppBundle\MappingProfiles;

use AppBundle\DataTransferObject\AddressDto;
use AppBundle\DataTransferObject\UserDto;
use AppBundle\Entity\User;
use BCC\AutoMapperBundle\Mapper\AbstractMap;
use BCC\AutoMapperBundle\Mapper\FieldFilter\ObjectMappingFilter;

class UserToDtoProfile extends AbstractMap
{

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
        $this->filter('address', new ObjectMappingFilter(AddressDto::class));
    }
}
