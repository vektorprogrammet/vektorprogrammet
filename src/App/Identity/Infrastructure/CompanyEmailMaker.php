<?php

namespace App\Identity\Infrastructure;

use App\Identity\Infrastructure\Entity\User;
use App\Support\Infrastructure\LogService;
use Doctrine\ORM\EntityManagerInterface;

class CompanyEmailMaker
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly LogService $logger,
    ) {
    }

    public function setCompanyEmailFor(User $user, $blackList)
    {
        $allCompanyEmails = $this->em->getRepository(User::class)->findAllCompanyEmails();
        $allEmails = array_merge($allCompanyEmails, $blackList);
        $firstName = strtolower((string) $this->replaceNorwegianCharacters($user->getFirstName()));
        $fullName = strtolower((string) $this->replaceNorwegianCharacters($user->getFullName()));

        $email = preg_replace('/\s+/', '.', $firstName).'@vektorprogrammet.no';
        if (array_search($email, $allEmails, true) !== false) {
            $email = preg_replace('/\s+/', '.', $fullName).'@vektorprogrammet.no';
        }

        $i = 2;
        while (array_search($email, $allEmails, true) !== false) {
            $email = preg_replace('/\s+/', '.', $fullName).$i.'@vektorprogrammet.no';
            ++$i;
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
        $map = [
            'æ' => 'ae', 'Æ' => 'ae', 'ø' => 'o', 'Ø' => 'o', 'å' => 'a', 'Å' => 'a',
            'é' => 'e', 'è' => 'e', 'É' => 'E', 'È' => 'E',
            'á' => 'a', 'à' => 'a', 'Á' => 'A', 'À' => 'A',
            'ö' => 'o', 'Ö' => 'O', 'ä' => 'a', 'Ä' => 'A', 'ü' => 'u', 'Ü' => 'U',
        ];
        $string = strtr($string, $map);
        $string = preg_replace('/[^A-Za-z0-9 ]/', '', $string);

        return $string;
    }
}
