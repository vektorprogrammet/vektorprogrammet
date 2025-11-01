<?php

namespace App\Contract;

use App\Models\Department;

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
    public function findNearestDepartment(array $departments): Department;

    /**
     * Find department closest to coordinates.
     *
     * @param array $coords
     * @return Department|null
     */
    public function findDepartmentClosestTo(array $coords): ?Department;

    /**
     * Find coordinates of current request.
     *
     * @return array|null
     */
    public function findCoordinatesOfCurrentRequest(): ?array;

    /**
     * Sort departments by distance from client.
     *
     * @param Department[] $departments
     * @return Department[]
     */
    public function sortDepartmentsByDistanceFromClient(array $departments): array;

    /**
     * Find coordinates for an IP address.
     *
     * @param string $ip
     * @return array|null
     */
    public function findCoordinates(string $ip): ?array;

    /**
     * Calculate distance between two coordinates.
     *
     * @param float $fromLat
     * @param float $fromLon
     * @param float $toLat
     * @param float $toLon
     * @return float
     */
    public function distance(float $fromLat, float $fromLon, float $toLat, float $toLon): float;

    /**
     * Get client IP address.
     *
     * @return string|null
     */
    public function clientIp(): ?string;
}
