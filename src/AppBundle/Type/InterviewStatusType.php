<?php

namespace AppBundle\Type;

abstract class InterviewStatusType
{
    const PENDING = 0;
    const ACCEPTED = 1;
    const REQUEST_NEW_TIME = 2;
    const CANCELLED = 3;
    const NO_CONTACT = 4;
}
