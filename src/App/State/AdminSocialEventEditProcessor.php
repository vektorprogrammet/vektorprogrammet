<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Department;
use App\Entity\Semester;
use App\Entity\SocialEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class AdminSocialEventEditProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): array
    {
        $id = $uriVariables['id'] ?? null;
        $event = $this->em->getRepository(SocialEvent::class)->find($id);

        if ($event === null) {
            throw new NotFoundHttpException('Social event not found.');
        }

        $event->setTitle($data->title);

        if ($data->description !== null) {
            $event->setDescription($data->description);
        }
        if ($data->startTime !== null) {
            $event->setStartTime(new \DateTime($data->startTime));
        }
        if ($data->endTime !== null) {
            $event->setEndTime(new \DateTime($data->endTime));
        }
        if ($data->link !== null) {
            $event->setLink($data->link);
        }
        if ($data->departmentId !== null) {
            $department = $this->em->getRepository(Department::class)->find($data->departmentId);
            if ($department === null) {
                throw new UnprocessableEntityHttpException('Department not found.');
            }
            $event->setDepartment($department);
        }
        if ($data->semesterId !== null) {
            $semester = $this->em->getRepository(Semester::class)->find($data->semesterId);
            if ($semester === null) {
                throw new UnprocessableEntityHttpException('Semester not found.');
            }
            $event->setSemester($semester);
        }

        $this->em->persist($event);
        $this->em->flush();

        return [
            'id' => $event->getId(),
            'title' => $event->getTitle(),
        ];
    }
}
