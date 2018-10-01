<?php


namespace AppBundle\Twig\Extension;

use AppBundle\Entity\Survey;
use AppBundle\Entity\User;

class TeamSurveyExtension extends \Twig_Extension
{
    public function getName()
    {
        return 'team_survey_extension';
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('get_next_survey', [$this, 'getNextSurvey',])
        );
    }

    public function getNextSurvey(User $user) : Survey
    {
    }
}
