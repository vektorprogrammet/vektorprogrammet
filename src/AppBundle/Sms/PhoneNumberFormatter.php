<?php


namespace AppBundle\Sms;

class PhoneNumberFormatter
{
    public static function format(string $number, $countryCode)
    {
        $number = preg_replace('/\s+/', '', $number);

        if (strlen($number) === 8) {
            return $countryCode . $number;
        } elseif (strlen($number) === 11 && substr($number, 0, strlen($countryCode) + 1) === "+{$countryCode}") {
            return $countryCode . substr($number, 3);
        } elseif (strlen($number) === 10 && substr($number, 0, strlen($countryCode)) === $countryCode) {
            return $number;
        } else {
            return false;
        }
    }
}
