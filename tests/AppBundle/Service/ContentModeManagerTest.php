<?php

namespace Tests\AppBundle\Service;

use AppBundle\Service\ContentModeManager;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class ContentModeManagerTest extends TestCase
{
    /**
     * @var ContentModeManager
     */
    private $service;

    /**
     * @var SessionInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private $session;

    protected function setUp()
    {
        $this->session = $this->createMock(SessionInterface::class);
        $this->service = new ContentModeManager($this->session);
    }

    public function testIsEditModeReturnsFalseByDefault()
    {
        $this->session->expects($this->once())
            ->method('get')
            ->with('edit-mode', false)
            ->willReturn(false);

        $result = $this->service->isEditMode();

        $this->assertFalse($result);
    }

    public function testIsEditModeReturnsTrueWhenSet()
    {
        $this->session->expects($this->once())
            ->method('get')
            ->with('edit-mode', false)
            ->willReturn(true);

        $result = $this->service->isEditMode();

        $this->assertTrue($result);
    }

    public function testChangeToEditMode()
    {
        $this->session->expects($this->once())
            ->method('set')
            ->with('edit-mode', true);

        $this->service->changeToEditMode();
    }

    public function testChangeToReadMode()
    {
        $this->session->expects($this->once())
            ->method('set')
            ->with('edit-mode', false);

        $this->service->changeToReadMode();
    }
}

