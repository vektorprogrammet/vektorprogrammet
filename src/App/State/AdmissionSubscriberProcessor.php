<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\ApiResource\AdmissionSubscriberInput;
use App\Entity\AdmissionSubscriber;
use App\Entity\Repository\AdmissionSubscriberRepository;
use App\Entity\Repository\DepartmentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class AdmissionSubscriberProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly DepartmentRepository $departmentRepo,
        private readonly AdmissionSubscriberRepository $subscriberRepo,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        assert($data instanceof AdmissionSubscriberInput);

        $department = $this->departmentRepo->find($data->departmentId);
        if (!$department) {
            throw new UnprocessableEntityHttpException('Department not found.');
        }

        // Silently ignore duplicate email+department combos
        $existing = $this->subscriberRepo->findByEmailAndDepartment($data->email, $department);
        if ($existing) {
            return;
        }

        $subscriber = new AdmissionSubscriber();
        $subscriber->setEmail($data->email);
        $subscriber->setDepartment($department);
        $subscriber->setInfoMeeting($data->infoMeeting);

        $this->em->persist($subscriber);
        $this->em->flush();
    }
}
