<?php


namespace AppBundle\Service;

use AppBundle\Entity\User;
use Doctrine\ORM\EntityManager;

class CompanyEmailMaker
{
    private $em;
    private $logger;

    public function __construct(EntityManager $em, LogService $logger)
    {
        $this->em = $em;
        $this->logger = $logger;
    }

    public function setCompanyEmailFor(User $user, $blackList)
    {
        $allCompanyEmails = $this->em->getRepository('AppBundle:User')->findAllCompanyEmails();
        $allEmails = array_merge($allCompanyEmails, $blackList);

        $email = preg_replace('/\s+/', '.', $user->getFirstName()) . '@vektorprogrammet.no';
        if (array_search($email, $allEmails) !== false) {
            $email = preg_replace('/\s+/', '.', $user->getFullName()) . '@vektorprogrammet.no';
        }

        $i = 2;
        while (array_search($email, $allEmails) !== false) {
            $email = preg_replace('/\s+/', '.', $user->getFullName()) . $i .'@vektorprogrammet.no';
            $i++;
        }

        $email = strtolower($email);
        $email = str_replace('æ', 'ae', $email);
        $email = str_replace('ø', 'o', $email);
        $email = str_replace('å', 'a', $email);

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->logger->alert("Failed to create email for $user. Invalid email: $email");
            return null;
        }

        $user->setCompanyEmail($email);
        $this->em->flush();
        $this->logger->info("Created company email, $email, for $user");
        return $email;
    }
}
