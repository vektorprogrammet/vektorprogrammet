<?php

namespace AppBundle\Service;

class JailService
{
    private $geoLocation;
    private $logger;

    public function __construct(GeoLocation $geoLocation, LogService $logger)
    {
        $this->geoLocation = $geoLocation;
        $this->logger = $logger;
    }

    public function banCurrentIpIfForeign()
    {
        $ip = $this->geoLocation->clientIp();
        $countryCode = $this->geoLocation->getCurrentCountryCode();
        $currentIpIsForeign = $ip !== null && $countryCode !== null && $countryCode !== 'NO';

        if ($currentIpIsForeign) {
            $this->banIp($ip);
        }
    }

    private function banIp(string $ip)
    {
        $countryCode = $this->geoLocation->getCurrentCountryCode();
        shell_exec("sudo fail2ban-client set vektorhell banip $ip");
        $this->logger->warning("$ip from $countryCode has been banned");
    }
}
