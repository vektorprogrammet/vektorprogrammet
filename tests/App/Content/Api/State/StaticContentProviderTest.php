<?php

namespace App\Tests\App\Content\Api\State;

use ApiPlatform\Metadata\Get;
use App\Content\Api\State\StaticContentByHtmlIdProvider;
use App\Content\Infrastructure\Entity\StaticContent;
use App\Content\Infrastructure\Repository\StaticContentRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class StaticContentProviderTest extends TestCase
{
    public function testProviderReturnsContentByHtmlId(): void
    {
        $content = $this->createMock(StaticContent::class);
        $content->method('getHtmlId')->willReturn('welcome-text');

        $repo = $this->createMock(StaticContentRepository::class);
        $repo->method('findOneByHtmlId')
            ->with('welcome-text')
            ->willReturn($content);

        $provider = new StaticContentByHtmlIdProvider($repo);
        $result = $provider->provide(new Get(), ['htmlId' => 'welcome-text']);

        $this->assertSame($content, $result);
    }

    public function testProviderThrowsNotFoundWhenHtmlIdDoesNotExist(): void
    {
        $this->expectException(NotFoundHttpException::class);

        $repo = $this->createMock(StaticContentRepository::class);
        $repo->method('findOneByHtmlId')->willReturn(null);

        $provider = new StaticContentByHtmlIdProvider($repo);
        $provider->provide(new Get(), ['htmlId' => 'nonexistent']);
    }
}
