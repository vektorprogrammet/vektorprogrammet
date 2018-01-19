<?php

namespace AppBundle\Event;

use AppBundle\Entity\InfoMeeting;
use Symfony\Component\EventDispatcher\Event;

class InfoMeetingEvent extends Event implements CrudEvent
{
    const CREATED = 'infoMeeting.created';
    const EDITED = 'infoMeeting.edited';
    const DELETED = 'infoMeeting.deleted';

    private $infoMeeting;

    /**
     * InfoMeetingEvent constructor.
     * @param $infoMeeting
     */
    public function __construct($infoMeeting)
    {
        $this->infoMeeting = $infoMeeting;
    }


    /**
     * @return string
     */
    public static function created(): string
    {
        return self::CREATED;
    }

    /**
     * @return string
     */
    public static function updated(): string
    {
        return self::EDITED;
    }

    /**
     * @return string
     */
    public static function deleted(): string
    {
        return self::DELETED;
    }

    /**
     * @return InfoMeeting
     */
    public function getObject()
    {
        return $this->infoMeeting;
    }
}
