<?php


namespace AppBundle\Service;

use AppBundle\Entity\SurveyNotification;
use AppBundle\Entity\SurveyNotifier;
use AppBundle\Entity\SurveyTaken;
use Doctrine\ORM\EntityManagerInterface;

class SurveyNotifierManager
{
    private $em;


    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function initializeSurveyNotifier(SurveyNotifier $surveyNotifier)
    {

        $this->em->persist($surveyNotifier);
        $survey = $surveyNotifier->getSurvey();
        $users = $surveyNotifier->getUsergroup()->getUsers();

        $notifications = array();
        foreach ($users as $user)
        {
            $isSurveyTakenByUser = !empty($this->em->getRepository(SurveyTaken::class)->findAllBySurveyAndUser($survey, $user));
            if($isSurveyTakenByUser)
            {
                continue;
            }
            $notification = new SurveyNotification();
            $notification->setUser($user);
            $notification->setSurveyNotifier($surveyNotifier);
            $notifications[] = $notification;
            $this->em->persist($notification);

        }
        $surveyNotifier->getUsergroup()->setIsInUse(true);
        $this->em->persist($surveyNotifier->getUsergroup());
        $this->em->flush();


    }



}
