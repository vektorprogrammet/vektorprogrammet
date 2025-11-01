<?php

namespace Tests\AppBundle\Service;

use AppBundle\Entity\Article;
use AppBundle\Entity\Department;
use AppBundle\Entity\User;
use AppBundle\Repository\Contract\ArticleRepositoryInterface;
use AppBundle\Repository\Contract\AssistantHistoryRepositoryInterface;
use AppBundle\Repository\Contract\DepartmentRepositoryInterface;
use AppBundle\Repository\Contract\UserRepositoryInterface;
use AppBundle\Service\Contract\GeoLocationInterface;
use AppBundle\Service\HomeService;
use PHPUnit\Framework\TestCase;

class HomeServiceTest extends TestCase
{
    /**
     * @var HomeService
     */
    private $service;

    /**
     * @var UserRepositoryInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private $userRepository;

    /**
     * @var ArticleRepositoryInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private $articleRepository;

    /**
     * @var DepartmentRepositoryInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private $departmentRepository;

    /**
     * @var AssistantHistoryRepositoryInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private $assistantHistoryRepository;

    /**
     * @var GeoLocationInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private $geoLocation;

    protected function setUp()
    {
        $this->userRepository = $this->createMock(UserRepositoryInterface::class);
        $this->articleRepository = $this->createMock(ArticleRepositoryInterface::class);
        $this->departmentRepository = $this->createMock(DepartmentRepositoryInterface::class);
        $this->assistantHistoryRepository = $this->createMock(AssistantHistoryRepositoryInterface::class);
        $this->geoLocation = $this->createMock(GeoLocationInterface::class);

        $this->service = new HomeService(
            $this->userRepository,
            $this->articleRepository,
            $this->departmentRepository,
            $this->assistantHistoryRepository,
            $this->geoLocation
        );
    }

    public function testGetHomePageData()
    {
        $assistants = [new User(), new User()];
        $teamMembers = [new User()];
        $articles = [new Article(), new Article()];
        $departments = [new Department()];
        $departmentsWithAdmission = [new Department()];
        $closestDepartment = new Department();
        $ipCoordinates = ['lat' => '63.0', 'lon' => '10.0'];

        $this->userRepository->expects($this->once())
            ->method('findAssistants')
            ->willReturn($assistants);

        $this->userRepository->expects($this->once())
            ->method('findTeamMembers')
            ->willReturn($teamMembers);

        $this->articleRepository->expects($this->once())
            ->method('findStickyAndLatestArticles')
            ->willReturn($articles);

        $this->departmentRepository->expects($this->once())
            ->method('findAll')
            ->willReturn($departments);

        $this->departmentRepository->expects($this->once())
            ->method('findAllWithActiveAdmission')
            ->willReturn($departmentsWithAdmission);

        $this->geoLocation->expects($this->once())
            ->method('sortDepartmentsByDistanceFromClient')
            ->with($departmentsWithAdmission)
            ->willReturn($departmentsWithAdmission);

        $this->geoLocation->expects($this->once())
            ->method('findNearestDepartment')
            ->with($departments)
            ->willReturn($closestDepartment);

        $this->geoLocation->expects($this->once())
            ->method('findCoordinatesOfCurrentRequest')
            ->willReturn($ipCoordinates);

        $this->assistantHistoryRepository->expects($this->once())
            ->method('numFemale')
            ->willReturn(300);

        $this->assistantHistoryRepository->expects($this->once())
            ->method('numMale')
            ->willReturn(500);

        $result = $this->service->getHomePageData();

        $this->assertIsArray($result);
        $this->assertEquals(602, $result['assistantCount']); // 2 + 600
        $this->assertEquals(161, $result['teamMemberCount']); // 1 + 160
        $this->assertEquals(300, $result['femaleAssistantCount']);
        $this->assertEquals(500, $result['maleAssistantCount']);
        $this->assertEquals($ipCoordinates, $result['ipWasLocated']);
        $this->assertEquals($departmentsWithAdmission, $result['departmentsWithActiveAdmission']);
        $this->assertEquals($closestDepartment, $result['closestDepartment']);
        $this->assertEquals($articles, $result['news']);
    }
}

