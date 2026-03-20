<?php

declare(strict_types=1);

namespace Tests\App\Controller;

use App\Operations\Infrastructure\Entity\AssistantHistory;
use App\Organization\Infrastructure\Entity\Department;
use Tests\BaseWebTestCase;

class AssistantHistoryControllerTest extends BaseWebTestCase
{
    private \Doctrine\ORM\EntityManagerInterface $em;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    /**
     * Regression test for AUTH-3: createAccessDeniedException() was called without throw,
     * making the access check a no-op. A team leader (dep-1 / NTNU, not admin) must
     * receive 403 when attempting to delete an assistant history belonging to a user in a
     * different department. The route requires ROLE_TEAM_LEADER, so we use teamleader.
     */
    public function testDeleteDeniedForTeamLeaderFromDifferentDepartment(): void
    {
        // teamleader uses fos-1 -> dep-1 (NTNU). Find an AssistantHistory whose user is
        // in a different department. Fixture ah4: user-3 has fos-4 -> dep-3 (NMBU).
        $teamLeaderDept = $this->em->getRepository(Department::class)->findOneBy(['shortName' => 'NTNU']);
        $this->assertNotNull($teamLeaderDept, 'Expected dep-1 (NTNU) department fixture');

        $crossDeptHistory = null;
        $histories = $this->em->getRepository(AssistantHistory::class)->findAll();
        foreach ($histories as $history) {
            if ($history->getUser()->getDepartment()->getId() !== $teamLeaderDept->getId()) {
                $crossDeptHistory = $history;
                break;
            }
        }

        $this->assertNotNull($crossDeptHistory, 'Expected an AssistantHistory in a department other than NTNU');

        $client = $this->createTeamLeaderClient();
        $client->request('POST', '/kontrollpanel/deltakerhistorikk/slett/' . $crossDeptHistory->getId());

        $this->assertEquals(403, $client->getResponse()->getStatusCode());
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->em->close();
    }
}
