<?php

namespace Tests\Unit\Repository;

use App\Repository\Contract\DepartmentRepositoryInterface;
use App\Repository\Contract\UserRepositoryInterface;
use App\Repository\Eloquent\DepartmentRepository;
use App\Repository\Eloquent\UserRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Base test class for comparing Eloquent and Doctrine repository implementations.
 *
 * This test suite verifies that Eloquent repository implementations
 * return the same results as their Doctrine counterparts.
 *
 * Note: This assumes both Doctrine and Eloquent access the same database.
 * In production, these tests should run against a shared test database.
 */
abstract class EloquentDoctrineComparisonTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Get the Doctrine repository instance.
     * Override this method in subclasses to return the specific Doctrine repository.
     *
     * @return object Doctrine repository instance
     */
    abstract protected function getDoctrineRepository(): object;

    /**
     * Get the Eloquent repository instance.
     * Override this method in subclasses to return the specific Eloquent repository.
     *
     * @return object Eloquent repository instance (implementing interface)
     */
    abstract protected function getEloquentRepository(): object;

    /**
     * Compare two result sets for equality.
     * Handles both arrays and collections.
     *
     * @param mixed $doctrineResult
     * @param mixed $eloquentResult
     * @return void
     */
    protected function assertResultsMatch($doctrineResult, $eloquentResult): void
    {
        // Convert both to arrays for comparison
        $doctrineArray = $this->normalizeResult($doctrineResult);
        $eloquentArray = $this->normalizeResult($eloquentResult);

        $this->assertCount(
            count($doctrineArray),
            $eloquentArray,
            'Result sets should have the same count'
        );

        // Compare IDs if they're entities/models
        $doctrineIds = $this->extractIds($doctrineArray);
        $eloquentIds = $this->extractIds($eloquentArray);

        sort($doctrineIds);
        sort($eloquentIds);

        $this->assertEquals(
            $doctrineIds,
            $eloquentIds,
            'Result sets should contain the same entities'
        );
    }

    /**
     * Normalize a result to an array.
     *
     * @param mixed $result
     * @return array
     */
    protected function normalizeResult($result): array
    {
        if (is_array($result)) {
            return $result;
        }

        if (is_object($result) && method_exists($result, 'toArray')) {
            return $result->toArray();
        }

        // Single entity/model
        return [$result];
    }

    /**
     * Extract IDs from a result set.
     *
     * @param array $results
     * @return array
     */
    protected function extractIds(array $results): array
    {
        $ids = [];

        foreach ($results as $result) {
            if (is_object($result)) {
                // Try common ID property/method names
                if (isset($result->id)) {
                    $ids[] = $result->id;
                } elseif (method_exists($result, 'getId')) {
                    $ids[] = $result->getId();
                } elseif (property_exists($result, 'id')) {
                    $ids[] = $result->id;
                }
            } elseif (is_array($result) && isset($result['id'])) {
                $ids[] = $result['id'];
            }
        }

        return $ids;
    }

    /**
     * Assert that a single entity matches between Doctrine and Eloquent.
     *
     * @param mixed $doctrineEntity
     * @param mixed $eloquentModel
     * @param array $attributesToCompare Attributes to compare (e.g., ['name', 'email'])
     * @return void
     */
    protected function assertEntityMatches($doctrineEntity, $eloquentModel, array $attributesToCompare): void
    {
        foreach ($attributesToCompare as $attribute) {
            $doctrineValue = $this->getAttributeValue($doctrineEntity, $attribute);
            $eloquentValue = $this->getAttributeValue($eloquentModel, $attribute);

            $this->assertEquals(
                $doctrineValue,
                $eloquentValue,
                "Attribute '{$attribute}' should match between Doctrine and Eloquent"
            );
        }
    }

    /**
     * Get attribute value from entity or model.
     *
     * @param mixed $entity
     * @param string $attribute
     * @return mixed
     */
    protected function getAttributeValue($entity, string $attribute)
    {
        if (is_object($entity)) {
            // Try getter method first
            $getter = 'get' . ucfirst($attribute);
            if (method_exists($entity, $getter)) {
                return $entity->$getter();
            }

            // Try property access
            if (isset($entity->$attribute)) {
                return $entity->$attribute;
            }

            // Try array access (for models)
            if (is_array($entity) && isset($entity[$attribute])) {
                return $entity[$attribute];
            }
        }

        return null;
    }
}

