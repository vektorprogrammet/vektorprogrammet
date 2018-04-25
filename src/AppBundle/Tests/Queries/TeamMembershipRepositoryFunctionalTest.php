<?php

namespace AppBundle\Tests\Queries;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TeamMembershipRepositoryFunctionalTest extends KernelTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        self::bootKernel();
        $this->em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    // A test to check whether the method finds the active work histories given the $today variable, and that the user has the same ID as the given user
    public function testFindActiveTeamMembershipsByUser()
    {

//		$workhistories = $this->em->getRepository('AppBundle:TeamMembership')->findActiveTeamMembershipsByUser(1);

//		$today = new \DateTime('now');

//		foreach ($workhistories as $wh){
//			$this->assertGreaterThan($wh->getStartSemester()->getStartDate(), $today);
//			if ( !($wh->getStartSemester()->getEndDate() == null )) $this->assertGreaterThan($today, $wh->getStartSemester()->getEndDate());
//			$this->assertEquals(1, $wh->getUser()->getId());
//		}
    }

    // A test to check whether the method finds the active work histories given the $today variable
    /*	public function testFindActiveTeamMemberships(){

            $workhistories = $this->em->getRepository('AppBundle:TeamMembership')->findActiveTeamMemberships();

            $today = new \DateTime('now');

            foreach ($workhistories as $wh){
                $this->assertGreaterThan($wh->getStartDate(), $today);
                if ( !($wh->getEndDate() == null )) $this->assertGreaterThan($today, $wh->getEndDate());
            }

        }*/

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        parent::tearDown();
        $this->em->close();
    }
}
