<?php
namespace AppBundle\MappingProfiles;

use AppBundle\DataTransferObject\SchoolDto;
use AppBundle\Entity\School;
use BCC\AutoMapperBundle\Mapper\AbstractMap;

class SchoolToDtoProfile extends AbstractMap {

    /**
     * @return string The source type
     */
    public function getSourceType()
    {
        return School::class;
    }

    /**
     * @return string The destination type
     */
    public function getDestinationType()
    {
        return SchoolDto::class;
    }

    public function __construct()
    {
        $this->buildDefaultMap();
    }
}