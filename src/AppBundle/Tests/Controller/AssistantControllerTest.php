<?php

namespace AppBundle\Tests\Controller;

use AppBundle\Tests\BaseWebTestCase;

class AssistantControllerTest extends BaseWebTestCase
{
    public function testCreateApplication()
    {
        $path = '/kontrollpanel/opptak';

        $applicationsBefore = $this->countTableRows($path);

        $this->createAndSubmitForm();

        $applicationsAfter = $this->countTableRows($path);

        $this->assertEquals($applicationsBefore + 1, $applicationsAfter);
    }

    public function testSameEmailCanOnlyBeUsedOnceInASemester()
    {
        $path = '/kontrollpanel/opptak';

        $applicationsBefore = $this->countTableRows($path);

        $this->createAndSubmitForm();
        $this->createAndSubmitForm();

        $applicationsAfter = $this->countTableRows($path);

        $this->assertEquals($applicationsBefore + 1, $applicationsAfter);
    }

    public function testSameEmailCanBeUsedInMultipleDepartments()
    {
        $firstDepartmentId = 1;
        $firstSemesterId = 2;
        $secondDepartmentId = 4;
        $secondDepartmentFieldOfStudyId = 5;
        $secondSemesterId = 3;
        $path = '/kontrollpanel/opptak';

        $applicationsBeforeFirstDepartment = $this->countTableRows("$path/$firstSemesterId");
        $applicationsBeforeSecondDepartment = $this->countTableRows("$path/$secondSemesterId");

        $this->createAndSubmitForm($firstDepartmentId);
        $this->createAndSubmitForm($secondDepartmentId, $secondDepartmentFieldOfStudyId);

        $applicationsAfterFirstDepartment = $this->countTableRows("$path/$firstSemesterId");
        $applicationsAfterSecondDepartment = $this->countTableRows("$path/$secondSemesterId");

        $this->assertEquals($applicationsBeforeFirstDepartment + 1, $applicationsAfterFirstDepartment);
        // A email can only be used once to send an application.
        $this->assertEquals($applicationsBeforeSecondDepartment, $applicationsAfterSecondDepartment);
    }

    private function createAndSubmitForm(int $departmentId = 1, int $fieldOfStudyId = 2)
    {
        $crawler = $this->anonymousGoTo("/opptak/avdeling/$departmentId");

        $form = $crawler->filter("form[name='application_$departmentId'] button:contains('Søk')")->form();

        $form["application_${departmentId}[user][firstName]"] = 'test';
        $form["application_${departmentId}[user][lastName]"] = 'mctest';
        $form["application_${departmentId}[user][email]"] = 'test@vektorprogrammet.no';
        $form["application_${departmentId}[user][phone]"] = '99887766';
        $form["application_${departmentId}[user][gender]"] = 0;
        $form["application_${departmentId}[user][fieldOfStudy]"] = $fieldOfStudyId;
        $form["application_${departmentId}[yearOfStudy]"] = '1. klasse';

        $this->createAnonymousClient()->submit($form);
    }
}
