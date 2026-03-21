<?php

namespace App\Tests\App\Organization\Api\Resource;

use App\Organization\Api\Resource\AdminTeamWriteResource;
use PHPUnit\Framework\TestCase;

class AdminTeamWriteResourceTest extends TestCase
{
    public function testAcceptApplicationAndActiveAndDeadlineFieldsExist(): void
    {
        $resource = new AdminTeamWriteResource();

        $this->assertNull($resource->acceptApplication);
        $this->assertNull($resource->active);
        $this->assertNull($resource->deadline);

        $resource->acceptApplication = true;
        $resource->active = false;
        $resource->deadline = '2026-04-01 12:00:00';

        $this->assertTrue($resource->acceptApplication);
        $this->assertFalse($resource->active);
        $this->assertSame('2026-04-01 12:00:00', $resource->deadline);
    }
}
