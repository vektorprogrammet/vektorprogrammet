<?php

namespace AppBundle\Entity;

interface PeriodInterface
{
    public function getStartDate(): \DateTime;
    public function getEndDate(): \Datetime;
}
