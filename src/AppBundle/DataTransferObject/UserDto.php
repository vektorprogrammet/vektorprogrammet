<?php


namespace AppBundle\DataTransferObject;

class UserDto
{
    public $firstName;
    public $lastName;
    public $fullName;
    public $username;
    public $email;
    public $companyEmail;
    public $isAdmin;
    public static function fromUser(\AppBundle\Entity\User $user): self
    {
        $dto = new self();
        $dto->firstName = $user->getFirstName();
        $dto->lastName = $user->getLastName();
        $dto->fullName = $user->getFullName();
        $dto->username = $user->getUserName();
        $dto->email = $user->getEmail();
        $dto->companyEmail = $user->getCompanyEmail();
        $dto->isAdmin = $user->isAdmin();
        return $dto;
    }
}
