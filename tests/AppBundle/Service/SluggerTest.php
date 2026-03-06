<?php

namespace Tests\App\Service;

use PHPUnit\Framework\TestCase;
use Symfony\Component\String\Slugger\AsciiSlugger;

class SluggerTest extends TestCase
{
    private AsciiSlugger $slugger;

    protected function setUp(): void
    {
        $this->slugger = new AsciiSlugger();
    }

    public function testTitleAsSlug()
    {
        $slug = $this->slugger->slug('This is a test title')->lower()->toString();
        self::assertEquals('this-is-a-test-title', $slug);
    }

    public function testNorwegianCharacters()
    {
        $slug = $this->slugger->slug('Test title æøå')->lower()->toString();
        self::assertStringContainsString('test-title', $slug);
        // ICU transliterates: æ→ae, ø→o, å→a
        self::assertMatchesRegularExpression('/test-title-[a-z]+/', $slug);
    }

    public function testSpecialCharacters()
    {
        $slug = $this->slugger->slug('Hello World!')->lower()->toString();
        self::assertEquals('hello-world', $slug);
    }
}
