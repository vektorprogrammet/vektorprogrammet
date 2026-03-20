<?php

namespace App\Shared\Contracts;

interface PeriodInterface
{
    public function getStartDate(): ?\Datetime;

    public function getEndDate(): ?\Datetime;
}
