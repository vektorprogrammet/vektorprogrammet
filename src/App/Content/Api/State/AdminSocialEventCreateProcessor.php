<?php

namespace App\Content\Api\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Department;
use App\Shared\Entity\Semester;
use App\Content\Infrastructure\Entity\SocialEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class AdminSocialEventCreateProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): array
    {
        $department = $data->departmentId
            ? $this->em->getRepository(Department::class)->find($data->departmentId)
            : null;

        if ($department === null) {
            throw new UnprocessableEntityHttpException('Invalid departmentId.');
        }

        $semester = $data->semesterId
            ? $this->em->getRepository(Semester::class)->find($data->semesterId)
            : null;

        if ($semester === null) {
            throw new UnprocessableEntityHttpException('Invalid semesterId.');
        }

        $event = new SocialEvent();
        $event->setTitle($data->title);
        $event->setDescription($data->description ?? '');
        $event->setDepartment($department);
        $event->setSemester($semester);

        if ($data->startTime !== null) {
            $event->setStartTime(new \DateTime($data->startTime));
        }
        if ($data->endTime !== null) {
            $event->setEndTime(new \DateTime($data->endTime));
        }
        if ($data->link !== null) {
            $event->setLink($data->link);
        }

        $this->em->persist($event);
        $this->em->flush();

        return ['id' => $event->getId()];
    }
}
