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
        setlocale(LC_ALL, 'nb_NO');
        $string = iconv('UTF-8', 'ASCII//TRANSLIT', $string); //Converts accents and norwegian characters
        $string = preg_replace("/[^A-Za-z0-9 ]/", '', $string); //Removes ' and `after iconv(), and other invalid characters
        return $string;
    }
}
