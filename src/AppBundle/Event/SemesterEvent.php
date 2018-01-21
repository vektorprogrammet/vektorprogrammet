<?php

namespace AppBundle\Event;

use AppBundle\Entity\Semester;
use Symfony\Component\EventDispatcher\Event;

class SemesterEvent extends Event implements CrudEvent
{
    const CREATED = 'semester.created';
    const EDITED = 'semester.edited';
    const DELETED = 'semester.deleted';

    private $semester;

    public function __construct(Semester $semester)
    {
        $this->semester = $semester;
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
     * @return Semester
     */
    public function getObject()
    {
        return $this->semester;
    }
}
