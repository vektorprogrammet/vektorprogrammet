<?php

namespace App\Operations\Api\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Operations\Api\Resource\AdminCertificateResource;
use App\Operations\Infrastructure\Repository\AssistantHistoryRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AdminCertificateProvider implements ProviderInterface
{
    public function __construct(
        private readonly AssistantHistoryRepository $assistantHistoryRepo,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): AdminCertificateResource
    {
        $id = $uriVariables['id'] ?? null;
        $assistantHistory = $id !== null ? $this->assistantHistoryRepo->find($id) : null;

        if ($assistantHistory === null) {
            throw new NotFoundHttpException('Assistant history not found.');
        }

        $resource = new AdminCertificateResource();
        $resource->id = $assistantHistory->getId();
        $resource->userName = $assistantHistory->getUser()->getFullName();
        $resource->schoolName = $assistantHistory->getSchool()?->getName();
        $resource->semesterName = (string) $assistantHistory->getSemester();
        $resource->departmentName = $assistantHistory->getDepartment()?->getName();
        $resource->workdays = $assistantHistory->getWorkdays();
        $resource->bolk = $assistantHistory->getBolk();
        $resource->day = $assistantHistory->getDay();

        return $resource;
    }
}
