<?php
namespace AppBundle\MappingProfiles;

use AppBundle\DataTransferObject\AddressDto;
use AppBundle\Entity\Address;
use BCC\AutoMapperBundle\Mapper\AbstractMap;

class AddressToDtoProfile extends AbstractMap {

    /**
     * @return string The source type
     */
    public function getSourceType()
    {
        return Address::class;
    }

    /**
     * @return string The destination type
     */
    public function getDestinationType()
    {
        return AddressDto::class;
    }


    public function __construct()
    {
        $this->buildDefaultMap();
    }
}