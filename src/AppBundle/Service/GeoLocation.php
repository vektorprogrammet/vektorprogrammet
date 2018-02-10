<?php

namespace AppBundle\Service;

use AppBundle\Entity\Department;
use Doctrine\ORM\EntityManagerInterface;
use Mpdf\Exception\InvalidArgumentException;

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

    /**
     * @param Department[] $departments
     * @return Department
     * @throws InvalidArgumentException
     */
    public function findNearestDepartment($departments)
    {
        if (empty($departments)) {
            throw new InvalidArgumentException('$departments cannot be empty');
        }

        return $this->sortDepartmentsByDistanceFromClient($departments)[0];
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

    /**
     * @param Department[] $departments
     * @return array
     */
    public function sortDepartmentsByDistanceFromClient($departments)
    {
        //$ip = '193.157.161.108'; // UIO
        //$ip = '158.39.3.40'; //NMBU
        $ip = $this->clientIp();
        $coords = $this->findCoordinates($ip);

        if ($coords === null) {
            return $departments;
        }
        usort($departments, function (Department $a, Department $b) use ($coords) {
            $fromLat = floatval($coords['lat']);
            $fromLon = floatval($coords['lon']);

            $aLat = floatval($a->getLatitude());
            $aLon = floatval($a->getLongitude());
            $aDistance = $this->distance($fromLat, $fromLon, $aLat, $aLon);

            $bLat = floatval($b->getLatitude());
            $bLon = floatval($b->getLongitude());
            $bDistance = $this->distance($fromLat, $fromLon, $bLat, $bLon);

            return $aDistance - $bDistance;
        });

        return $departments;
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
