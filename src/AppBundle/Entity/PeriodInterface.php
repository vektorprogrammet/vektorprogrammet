<?php

namespace AppBundle\Entity;

use Datetime;

interface PeriodInterface
{
    public function getStartDate(): ? DateTime;
    public function getEndDate(): ? Datetime;
}
