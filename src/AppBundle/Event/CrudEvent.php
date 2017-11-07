<?php

namespace AppBundle\Event;


interface CrudEvent
{
    public function getObject();
    public static function created(): string;
    public static function updated(): string;
    public static function deleted(): string;
}
