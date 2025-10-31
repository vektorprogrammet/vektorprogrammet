<?php

namespace AppBundle\Service\Contract;

use AppBundle\Entity\Department;

/**
 * Interface for GeoLocation service.
 * Defines contract for geolocation operations.
 */
interface GeoLocationInterface
{
    /**
     * Find the nearest department from a list of departments.
     *
     * @param Department[] $departments
     * @return Department
     */
    public function findNearestDepartment($departments);

    /**
     * Find department closest to coordinates.
     *
     * @param array $coords
     * @return Department|null
     */
    public function findDepartmentClosestTo($coords);

    /**
     * Find coordinates of current request.
     *
     * @return array|null
     */
    public function findCoordinatesOfCurrentRequest();

    /**
     * Sort departments by distance from client.
     *
     * @param Department[] $departments
     * @return Department[]
     */
    public function sortDepartmentsByDistanceFromClient($departments);

    /**
     * Find coordinates for an IP address.
     *
     * @param string $ip
     * @return array|null
     */
    public function findCoordinates($ip);

    /**
     * Calculate distance between two coordinates.
     *
     * @param float $fromLat
     * @param float $fromLon
     * @param float $toLat
     * @param float $toLon
     * @return float
     */
    public function distance(float $fromLat, float $fromLon, float $toLat, float $toLon);

    /**
     * Get client IP address.
     *
     * @return string|null
     */
    public function clientIp();
}
