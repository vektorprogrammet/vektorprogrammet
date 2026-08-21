<?php

declare(strict_types=1);

namespace Tests\Fixtures;

use App\Content\Infrastructure\Entity\Article;
use App\Identity\Infrastructure\Entity\Role;
use App\Identity\Infrastructure\Entity\User;
use App\Admission\Infrastructure\Entity\AdmissionPeriod;
use App\Operations\Infrastructure\Entity\AssistantHistory;
use App\Organization\Infrastructure\Entity\Department;
use App\Organization\Infrastructure\Entity\ExecutiveBoard;
use App\Organization\Infrastructure\Entity\FieldOfStudy;
use App\Organization\Infrastructure\Entity\Team;
use App\Scheduling\Infrastructure\Entity\School;
use App\Shared\Entity\Semester;
use App\Survey\Infrastructure\Entity\Survey;
use App\Survey\Infrastructure\Entity\SurveyQuestion;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;

final class CoreUserJourneyFixture extends AbstractFixture implements FixtureGroupInterface
{
    public static function getGroups(): array
    {
        return ['core-user-journeys'];
    }

    public function load(ObjectManager $manager): void
    {
        $now = new \DateTimeImmutable('now');

        $role = new Role('ROLE_TEAM_MEMBER');
        $role->setName('Core journey team member');
        $manager->persist($role);

        $department = new Department();
        $department->setName('Core journey department');
        $department->setShortName('CORE');
        $department->setEmail('core-journeys@example.invalid');
        $department->setCity('Core City');
        $department->setAddress('Core journey only');
        $department->setLatitude('63.4305');
        $department->setLongitude('10.3951');
        $manager->persist($department);

        $fieldOfStudy = new FieldOfStudy();
        $fieldOfStudy->setName('Core journey studies');
        $fieldOfStudy->setShortName('CORE-STUDY');
        $fieldOfStudy->setDepartment($department);
        $department->addFieldOfStudy($fieldOfStudy);
        $manager->persist($fieldOfStudy);

        $semester = new Semester();
        $semester->setSemesterTime($now->format('n') < 8 ? 'Vår' : 'Høst');
        $semester->setYear($now->format('Y'));
        $manager->persist($semester);

        $admissionPeriod = new AdmissionPeriod();
        $admissionPeriod->setDepartment($department);
        $admissionPeriod->setSemester($semester);
        $admissionPeriod->setStartDate((new \DateTime('yesterday')));
        $admissionPeriod->setEndDate((new \DateTime('+14 days')));
        $department->addAdmissionPeriod($admissionPeriod);
        $manager->persist($admissionPeriod);

        $team = new Team();
        $team->setName('Core journey team');
        $team->setEmail('core-team@example.invalid');
        $team->setDepartment($department);
        $team->setDescription('Team used by the core user journey fixture.');
        $team->setShortDescription('Core journey team');
        $team->setAcceptApplication(true);
        $team->setDeadline(new \DateTime('+14 days'));
        $team->setActive(true);
        $manager->persist($team);

        $board = new ExecutiveBoard();
        $board->setName('Core journey board');
        $board->setEmail('core-board@example.invalid');
        $board->setDescription('Board used by the public contact page.');
        $manager->persist($board);

        $school = new School();
        $school->setName('Core journey school');
        $school->setContactPerson('Core contact');
        $school->setEmail('core-school@example.invalid');
        $school->setPhone('00000000');
        $school->setInternational(false);
        $school->setActive(true);
        $department->addSchool($school);
        $school->addDepartment($department);
        $manager->persist($school);

        $user = $this->createUser(
            'core-journey-user-0032',
            'core-journey-user-0032@example.invalid',
            'Core',
            'Journey',
            $role,
            $fieldOfStudy,
            'core-journey-password-0032',
        );
        $manager->persist($user);

        $assistantHistory = new AssistantHistory();
        $assistantHistory->setUser($user);
        $assistantHistory->setSemester($semester);
        $assistantHistory->setDepartment($department);
        $assistantHistory->setSchool($school);
        $assistantHistory->setWorkdays('1');
        $assistantHistory->setBolk('Bolk 1');
        $assistantHistory->setDay('Mandag');
        $manager->persist($assistantHistory);

        $surveyQuestion = new SurveyQuestion();
        $surveyQuestion->setQuestion('Hva er din favorittfarge?');
        $surveyQuestion->setHelp('Svar med ett ord.');
        $surveyQuestion->setOptional(false);
        $surveyQuestion->setType('text');
        $manager->persist($surveyQuestion);

        $survey = new Survey();
        $survey->setSemester($semester);
        $survey->setDepartment($department);
        $survey->setName('Core anonymous survey');
        $survey->setFinishPageContent('Core survey complete.');
        $survey->setConfidential(false);
        $survey->setTargetAudience(Survey::$SCHOOL_SURVEY);
        $survey->addSurveyQuestion($surveyQuestion);
        $manager->persist($survey);

        $article = new Article();
        $article->setTitle('Core journey article');
        $article->setSlug('core-journey-article');
        $article->setArticle('<p>Core journey content rendered by Symfony.</p>');
        $article->setImageLarge('images/vektor.png');
        $article->setImageSmall('images/vektor.png');
        $article->setPublished(true);
        $article->setSticky(false);
        $article->setCreated(new \DateTime('now'));
        $article->setUpdated(new \DateTime('now'));
        $manager->persist($article);

        $manager->flush();
    }

    private function createUser(
        string $username,
        string $email,
        string $firstName,
        string $lastName,
        Role $role,
        FieldOfStudy $fieldOfStudy,
        string $password,
    ): User {
        $user = new User();
        $user->setActive(true);
        $user->setEmail($email);
        $user->setFirstName($firstName);
        $user->setLastName($lastName);
        $user->setGender(false);
        $user->setPhone('00000000');
        $user->setUserName($username);
        $user->setPassword($password);
        $user->setFieldOfStudy($fieldOfStudy);
        $user->setPicturePath('images/defaultProfile.png');
        $user->addRole($role);

        return $user;
    }
}
