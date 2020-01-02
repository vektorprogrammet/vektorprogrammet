<?php

namespace Tests\AppBundle\Command;

use AppBundle\Entity\User;
use AppBundle\Service\CompanyEmailMaker;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CompanyEmailMakerTest extends KernelTestCase
{
    /**
     * @var CompanyEmailMaker
     */
    private $emailMaker;

    protected function setUp()
    {
        $kernel = $this->createKernel();
        $kernel->boot();

        $this->emailMaker = $kernel->getContainer()->get(CompanyEmailMaker::class);
    }

    public function testFirstNameAsEmail()
    {
        $user = new User();
        $user->setFirstName('TestEmailMaker');
        $user->setLastName('TestLast');

        $this->emailMaker->setCompanyEmailFor($user, []);
        self::assertEquals('testemailmaker@vektorprogrammet.no', $user->getCompanyEmail());
    }

    public function testFullNameAsEmail()
    {
        $user = new User();
        $user->setFirstName('Petter');
        $user->setLastName('TestLast');

        $this->emailMaker->setCompanyEmailFor($user, []);
        // petter@vektorprogrammet.no is already in use
        self::assertEquals('petter.testlast@vektorprogrammet.no', $user->getCompanyEmail());
    }

    public function testFullNameAlreadyInUse()
    {
        $user = new User();
        $user->setFirstName('Petter');
        $user->setLastName('Johansen');

        $blackList = ['petter.johansen@vektorprogrammet.no', 'petter.johansen2@vektorprogrammet.no'];

        $this->emailMaker->setCompanyEmailFor($user, $blackList);
        self::assertEquals('petter.johansen3@vektorprogrammet.no', $user->getCompanyEmail());
    }

    public function testNorwegianCharacters()
    {
        $user = new User();
        $user->setFirstName('FirstæØå');
        $user->setLastName('LastÆøå');

        $blackList = ['firstaeoa@vektorprogrammet.no'];

        $this->emailMaker->setCompanyEmailFor($user, $blackList);
        self::assertEquals('firstaeoa.lastaeoa@vektorprogrammet.no', $user->getCompanyEmail());
    }
    public function testAccentCharacters()
    {
        $user = new User();
        $user->setFirstName('Firstèéàá');
        $user->setLastName('LastÀÁÈÉ');

        $blackList = ['firsteeaa@vektorprogrammet.no'];

        $this->emailMaker->setCompanyEmailFor($user, $blackList);
        self::assertEquals('firsteeaa.lastaaee@vektorprogrammet.no', $user->getCompanyEmail());

    }
}
