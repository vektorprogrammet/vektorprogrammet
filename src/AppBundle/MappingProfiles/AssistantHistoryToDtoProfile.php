<?php
namespace AppBundle\MappingProfiles;

use AppBundle\DataTransferObject\AssistantHistoryDto;
use AppBundle\DataTransferObject\SchoolDto;
use AppBundle\DataTransferObject\UserDto;
use AppBundle\Entity\AssistantHistory;
use BCC\AutoMapperBundle\Mapper\AbstractMap;
use BCC\AutoMapperBundle\Mapper\FieldFilter\ObjectMappingFilter;

class AssistantHistoryToDtoProfile extends AbstractMap {

    /**
     * @return string The source type
     */
    public function getSourceType()
    {
        return AssistantHistory::class;
    }

    /**
     * @return string The destination type
     */
    public function getDestinationType()
    {
        return AssistantHistoryDto::class;
    }

    public function __construct()
    {
        $this->buildDefaultMap();
        $this->filter('user', new ObjectMappingFilter(UserDto::class));
        $this->filter('school', new ObjectMappingFilter(SchoolDto::class));
    }

}