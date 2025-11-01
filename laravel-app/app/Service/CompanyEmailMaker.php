<?php


namespace App\Service;

use App\Models\User;
use App\Repository\Contract\UserRepositoryInterface;
use App\Service\Contract\CompanyEmailMakerInterface;
use App\Service\Contract\LogServiceInterface;

class CompanyEmailMaker implements CompanyEmailMakerInterface
{
    private UserRepositoryInterface $userRepository;
    private LogServiceInterface $logger;

    public function __construct(
        UserRepositoryInterface $userRepository,
        LogServiceInterface $logger
    ) {
        $this->userRepository = $userRepository;
        $this->logger = $logger;
    }

    public function setCompanyEmailFor(User $user, array $blackList): ?string
    {
        $allCompanyEmails = $this->userRepository->findAllCompanyEmails();
        $allEmails = array_merge($allCompanyEmails, $blackList);
        $firstName = strtolower($this->replaceNorwegianCharacters($user->first_name));
        $fullName = strtolower($this->replaceNorwegianCharacters($user->first_name . ' ' . $user->last_name));


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

        $user->company_email = $email;
        $user->save();
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
