<?php

namespace AppBundle\DataFixtures;

/** Keeps the existing globally unique fixture names when loading a fresh local/test database. */
abstract class Fixture extends \Doctrine\Common\DataFixtures\AbstractFixture
{
    public function getReference(string $name, ?string $class = null): object
    {
        if ($class === null) {
            $matches = [];
            foreach ($this->referenceRepository->getReferencesByClass() as $referenceClass => $references) {
                if (array_key_exists($name, $references)) {
                    $matches[] = $referenceClass;
                }
            }
            if (count($matches) !== 1) {
                throw new \LogicException(sprintf('Fixture reference "%s" must identify exactly one class.', $name));
            }
            $class = $matches[0];
        }

        return parent::getReference($name, $class);
    }
}
