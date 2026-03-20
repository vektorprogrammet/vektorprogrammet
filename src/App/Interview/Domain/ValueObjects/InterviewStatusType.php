<?php

namespace App\Interview\Domain\ValueObjects;

abstract class InterviewStatusType
{
    public const PENDING = 0;
    public const ACCEPTED = 1;
    public const REQUEST_NEW_TIME = 2;
    public const CANCELLED = 3;
    public const NO_CONTACT = 4;
}
