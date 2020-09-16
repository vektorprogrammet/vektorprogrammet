<?php

namespace AppBundle\EventSubscriber;

use AppBundle\Entity\AdmissionNotification;
use AppBundle\Entity\InterviewAnswer;
use AppBundle\Entity\InterviewQuestion;
use AppBundle\Entity\InterviewQuestionAlternative;
use AppBundle\Entity\InterviewScore;
use AppBundle\Entity\PasswordReset;
use AppBundle\Entity\SurveyAnswer;
use AppBundle\Entity\SurveyQuestion;
use AppBundle\Entity\SurveyQuestionAlternative;
use AppBundle\Entity\SurveyTaken;
use AppBundle\Entity\UnhandledAccessRule;
use AppBundle\Entity\User;
use AppBundle\Role\Roles;
use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class DbSubscriber implements EventSubscriber
{
    private $logger;
    private $ignoredClasses;
    private $manager;

    public function __construct(LoggerInterface $logger, EntityManagerInterface $manager, string $env)
    {
        $this->logger = $logger;
        $this->ignoredClasses = [
            InterviewAnswer::class,
            InterviewQuestion::class,
            InterviewQuestionAlternative::class,
            InterviewScore::class,
            PasswordReset::class,
            SurveyAnswer::class,
            SurveyQuestion::class,
            SurveyQuestionAlternative::class,
            AdmissionNotification::class,
            SurveyTaken::class,
        ];
        if ($env === 'staging') {
            $this->ignoredClasses[] = UnhandledAccessRule::class;
        }
        $this->manager = $manager;
    }

    /**
     * Returns an array of events this subscriber wants to listen to.
     *
     * @return array
     */
    public function getSubscribedEvents()
    {
        return array(
            'prePersist',
            'postPersist',
            'postUpdate',
            'postRemove'
        );
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $obj = $args->getObject();

        if ($obj instanceof User) {
            $this->setDefaultUserRole($obj);
        }
    }

    private function setDefaultUserRole(User $user)
    {
        if (!empty($user->getRoles())) {
            return;
        }

        $defaultRole = $this->manager->getRepository('AppBundle:Role')->findByRoleName(Roles::ASSISTANT);
        $user->addRole($defaultRole);
    }

    public function postUpdate(LifecycleEventArgs $args)
    {
        $this->log($args, 'Updated');
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $this->log($args, 'Created');
    }

    public function postRemove(LifecycleEventArgs $args)
    {
        $this->log($args, 'Deleted');
    }

    private function log(LifecycleEventArgs $args, string $action)
    {
        $obj = $args->getObject();
        $className = get_class($obj);

        if (in_array($className, $this->ignoredClasses)) {
            return;
        }

        $lastSlashIdx = strrpos($className, "\\");
        if (false !== $lastSlashIdx) {
            $className = substr($className, $lastSlashIdx + 1);
        }

        $objName = $this->getObjectName($obj);

        $this->logger->info("$action $className $objName");
    }

    private function getObjectName($obj)
    {
        $name = "";
        if (method_exists($obj, '__toString')) {
            $name = "*{$obj->__toString()}*";
        }

        return $name;
    }
}
