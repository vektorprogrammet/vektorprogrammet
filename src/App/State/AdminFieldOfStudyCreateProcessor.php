<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\FieldOfStudy;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

class AdminFieldOfStudyCreateProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly Security $security,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): array
    {
        $user = $this->security->getUser();

        $fieldOfStudy = new FieldOfStudy();
        $fieldOfStudy->setName($data->name);
        $fieldOfStudy->setShortName($data->shortName);
        $fieldOfStudy->setDepartment($user->getFieldOfStudy()->getDepartment());

        $this->em->persist($fieldOfStudy);
        $this->em->flush();

        return ['id' => $fieldOfStudy->getId()];
    }
}
