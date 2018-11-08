<?php

namespace Tests\AppBundle\Controller;

use Tests\BaseWebTestCase;

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
        $secondDepartmentId = 4;
        $secondDepartmentFieldOfStudyId = 5;
        $firstDepartmentPath = "/kontrollpanel/opptak?department=$firstDepartmentId&semester=1";
        $secondDepartmentPath = "/kontrollpanel/opptak?department=$secondDepartmentId&semester=1";

        $applicationsBeforeFirstDepartment = $this->countTableRows($firstDepartmentPath);
        $applicationsBeforeSecondDepartment = $this->countTableRows($secondDepartmentPath);

        $this->createAndSubmitForm($firstDepartmentId);
        $this->createAndSubmitForm($secondDepartmentId, $secondDepartmentFieldOfStudyId);

        $applicationsAfterFirstDepartment = $this->countTableRows($firstDepartmentPath);
        $applicationsAfterSecondDepartment = $this->countTableRows($secondDepartmentPath);

        $this->assertEquals($applicationsBeforeFirstDepartment + 1, $applicationsAfterFirstDepartment);
        $this->assertEquals($applicationsBeforeSecondDepartment + 1, $applicationsAfterSecondDepartment);
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
