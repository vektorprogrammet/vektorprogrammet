<?php

namespace AppBundle\Service;

use Doctrine\ORM\EntityManagerInterface;

class GeoLocation
{
    private $ipinfoToken;
    private $departmentRepo;

    /**
     * GeoLocation constructor.
     *
     * @param string $ipinfoToken
     * @param EntityManagerInterface $em
     */
    public function __construct(string $ipinfoToken, EntityManagerInterface $em)
    {
        $this->ipinfoToken = $ipinfoToken;
        $this->departmentRepo = $em->getRepository('AppBundle:Department');
    }

    public function findNearestDepartment()
    {
        $ip = $this->clientIp();
        if (!$ip) {
            return null;
        }

        $coords = $this->findCoordinates($ip);
        if (!$coords) {
            return null;
        }

        return $this->findDepartmentClosestTo($coords);
    }

    public function findDepartmentClosestTo($coords)
    {
        $departments = $this->departmentRepo->findAll();
        if (count($departments) < 1) {
            return null;
        }

        $closestDepartment = null;
        $shortestDistance = -1;
        foreach ($departments as $department) {
            $fromLat = floatval($coords['lat']);
            $fromLon = floatval($coords['lon']);
            $toLat = floatval($department->getLatitude());
            $toLon = floatval($department->getLongitude());
            $distance = $this->distance($fromLat, $fromLon, $toLat, $toLon);

            if ($shortestDistance < 0 || $distance < $shortestDistance) {
                $closestDepartment = $department;
                $shortestDistance = $distance;
            }
        }

        return $closestDepartment;
    }

    public function findCoordinates($ip)
    {
        $response = file_get_contents("http://ipinfo.io/$ip?token={$this->ipinfoToken}");
        $location = json_decode($response, true);
        if (!isset($location['loc'])) {
            return null;
        }

        $coords = explode(',', $location['loc']);
        if (count($coords) !== 2) {
            return null;
        }

        return [
            'lat' => $coords[0],
            'lon' => $coords[1]
        ];
    }

    public function distance(float $fromLat, float $fromLon, float $toLat, float $toLon)
    {
        $theta = $fromLon - $toLon;
        $dist = sin(deg2rad($fromLat)) * sin(deg2rad($toLat)) +  cos(deg2rad($fromLat)) * cos(deg2rad($toLat)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        return $dist * 60 * 1.1515 * 1609.344;
    }

    public function clientIp()
    {
        if (isset($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif (isset($_SERVER['HTTP_X_FORWARDED'])) {
            return $_SERVER['HTTP_X_FORWARDED'];
        } elseif (isset($_SERVER['HTTP_FORWARDED_FOR'])) {
            return $_SERVER['HTTP_FORWARDED_FOR'];
        } elseif (isset($_SERVER['HTTP_FORWARDED'])) {
            return $_SERVER['HTTP_FORWARDED'];
        } elseif (isset($_SERVER['REMOTE_ADDR'])) {
            return $_SERVER['REMOTE_ADDR'];
        } else {
            return null;
        }
    }
}
