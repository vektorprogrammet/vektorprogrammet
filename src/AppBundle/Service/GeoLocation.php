<?php

namespace AppBundle\Service;

use AppBundle\Entity\Department;
use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;
use Symfony\Component\Debug\Exception\ContextErrorException;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;

class GeoLocation
{
    private $ipinfoToken;
    private $departmentRepo;
    private $session;
    private $requestStack;
    private $logger;
    /**
     * @var array
     */
    private $ignoredAsns;

    private $geoDataCache;

    /**
     * GeoLocation constructor.
     *
     * @param string $ipinfoToken
     * @param array $ignoredAsns
     * @param EntityManagerInterface $em
     * @param Session $session
     * @param RequestStack $requestStack
     * @param LogService $logger
     */
    public function __construct(string $ipinfoToken, array $ignoredAsns, EntityManagerInterface $em, Session $session, RequestStack $requestStack, LogService $logger)
    {
        $this->ipinfoToken = $ipinfoToken;
        $this->departmentRepo = $em->getRepository('AppBundle:Department');
        $this->session = $session;
        $this->requestStack = $requestStack;
        $this->logger = $logger;
        $this->ignoredAsns = $ignoredAsns;
        $this->geoDataCache = [];
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

    public function findCoordinatesOfCurrentRequest()
    {
        $ip = $this->clientIp();
        if ($ip === null) {
        	return null;
        }

        return $this->findCoordinates($ip);
    }

    /**
     * @param Department[] $departments
     * @return Department[] $departments
     */
    public function sortDepartmentsByDistanceFromClient($departments)
    {
//        $ip = '158.39.3.40'; // Oslo
//        $ip = '146.185.181.87'; // Server location (Amsterdam)
//        $ip = '129.241.56.201'; // Trondheim
//        $ip = '46.230.133.85'; // Mobile (Oslo)

        $coords = null;
        $ip = $this->clientIp();
        if ($ip !== null) {
	        $coords = $this->findCoordinates($ip);
        }

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

    public function findCoordinates(string $ip)
    {
        $ignoreGeo = $this->requestStack->getMasterRequest()->headers->get('ignore-geo');
        if (!$this->ipinfoToken || $ignoreGeo) {
            return null;
        }

        $coords = $this->session->get('coords');
        if ($coords) {
            return $coords;
        }

        $response = $this->fetchGeoData($ip);
        if ($response === null) {
        	return null;
        }

        $coords = explode(',', $response['loc']);
        if (count($coords) !== 2) {
            $this->logger->warning("Could not find lat/lon in location 
                object. \nLocation:\n$response");
            return null;
        }

        $coords = [
            'lat' => $coords[0],
            'lon' => $coords[1]
        ];

        $this->session->set('coords', $coords);

        return $coords;
    }

    public function distance(float $fromLat, float $fromLon, float $toLat, float $toLon)
    {
        $theta = $fromLon - $toLon;
        $dist = sin(deg2rad($fromLat)) * sin(deg2rad($toLat)) +  cos(deg2rad($fromLat)) * cos(deg2rad($toLat)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        return $dist * 60 * 1.1515 * 1609.344;
    }

    public function getCurrentCountryCode() {
	    $ip = $this->clientIp();
	    if ($ip === null) {
	    	return null;
	    }

	    $response = $this->fetchGeoData($ip);
	    if ($response === null) {
		    return null;
	    }

	    return $response['country'];
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

    private function fetchGeoData(string $ip) {
	    if (isset($this->geoDataCache[$ip])) {
		    return $this->geoDataCache[$ip];
	    }

	    try {
		    $rawResponse = file_get_contents("http://ipinfo.io/$ip?token={$this->ipinfoToken}");
		    $response = json_decode($rawResponse, true);
	    } catch (ContextErrorException $exception) {
		    $this->logger->warning("Could not get location from 
            ipinfo.io. The page returned an error.\nError:\n
            {$exception->getMessage()}");
		    $this->geoDataCache[$ip] = null;
		    return null;
	    }

	    if (!isset($response['org'])) {
		    $this->logger->warning("Could not get org from 
            ipinfo.io.\nResponse:\n$rawResponse");
		    $response = null;
	    } else if ($this->ipIsFromAnIgnoredAsn($response)) {
		    $response = null;
	    } else if (!isset($response['loc'])) {
		    $this->logger->warning("Could not get location from 
            ipinfo.io.\nResponse:\n$rawResponse");
		    $response = null;
	    } else if (!isset($response['country'])) {
		    $this->logger->warning("Could not get country from 
            ipinfo.io.\nResponse:\n$rawResponse");
		    $response = null;
	    }

	    $this->geoDataCache[$ip] = $response;

	    return $response;
    }

    private function ipIsFromAnIgnoredAsn($response) : bool
    {
        if (!isset($response['org'])) {
            return false;
        }

        foreach ($this->ignoredAsns as $asn) {
            if (strpos($response['org'], $asn) !== false) {
                return true;
            }
        }

        return false;
    }
}
