<?php

namespace App\Tests\App\Support;

use ApiPlatform\OpenApi\Factory\OpenApiFactoryInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class OpenApiExportTest extends KernelTestCase
{
    public function testApplicationSerializerProducesStandardOpenApiDocument(): void
    {
        self::bootKernel();

        $container = self::getContainer();
        $serializer = $container->get('serializer');
        $factory = $container->get(OpenApiFactoryInterface::class);
        $document = $serializer->normalize($factory(), 'json', ['spec_version' => '3']);

        self::assertIsArray($document);
        self::assertArrayHasKey('paths', $document);
        self::assertArrayNotHasKey('extensionProperties', $document);

        $paths = $document['paths'];
        self::assertIsArray($paths);
        self::assertNotEmpty($paths);
        foreach (array_keys($paths) as $path) {
            self::assertStringStartsWith('/', $path);
        }
        self::assertArrayNotHasKey('paths', $paths);

        $components = $document['components'] ?? null;
        self::assertIsArray($components);
        $assertNoExtensionProperties = static function (mixed $value) use (&$assertNoExtensionProperties): void {
            if (!is_array($value)) {
                return;
            }

            self::assertArrayNotHasKey('extensionProperties', $value);
            foreach ($value as $nested) {
                $assertNoExtensionProperties($nested);
            }
        };
        $assertNoExtensionProperties($components);
    }
}
