<?php

namespace App\Entity;

interface PeriodInterface
{
    public function getStartDate(): ?\Datetime;

    public function getEndDate(): ?\Datetime;
}
