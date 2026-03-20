<?php

namespace App\Shared\Contracts;

interface PeriodInterface
{
    public function getStartDate(): ?\DateTime;

    public function getEndDate(): ?\DateTime;
}
