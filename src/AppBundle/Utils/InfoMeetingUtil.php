<?php

namespace AppBundle\Utils;

use AppBundle\Entity\InfoMeeting;

class InfoMeetingUtil
{
    public static function shouldSendInfoMeetingNotification(?InfoMeeting $infoMeeting): bool
    {
        return $infoMeeting !== null &&
            $infoMeeting->getDate() !== null &&
            $infoMeeting->isShowOnPage() &&
            TimeUtil::dateIsToday($infoMeeting->getDate());
    }
}
