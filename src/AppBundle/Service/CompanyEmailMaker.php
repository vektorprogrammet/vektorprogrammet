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
        $firstName = strtolower($this->replaceNorwegianCharacters($user->getFirstName()));
        $fullName = strtolower($this->replaceNorwegianCharacters($user->getFullName()));

        $email = preg_replace('/\s+/', '.', $firstName) . '@vektorprogrammet.no';
        if (array_search($email, $allEmails) !== false) {
            $email = preg_replace('/\s+/', '.', $fullName) . '@vektorprogrammet.no';
        }

        $i = 2;
        while (array_search($email, $allEmails) !== false) {
            $email = preg_replace('/\s+/', '.', $fullName) . $i .'@vektorprogrammet.no';
            $i++;
        }


        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->logger->alert("Failed to create email for $user. Invalid email: $email");
            return null;
        }

        $user->setCompanyEmail($email);
        $this->em->flush();
        $this->logger->info("Created company email, $email, for $user");
        return $email;
    }

    private function replaceNorwegianCharacters($string)
    {
        $string = str_replace('æ', 'ae', $string);
        $string = str_replace('Æ', 'AE', $string);
        $string = str_replace('ø', 'o', $string);
        $string = str_replace('Ø', 'O', $string);
        $string = str_replace('å', 'a', $string);
        $string = str_replace('Å', 'Å', $string);

        return $string;
    }
}
