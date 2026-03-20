<?php

namespace App\Admission\Api\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Admission\Api\Resource\AdminApplicationBulkDeleteInput;
use App\Admission\Infrastructure\Entity\Application;
use Doctrine\ORM\EntityManagerInterface;

class AdminApplicationBulkDeleteProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        assert($data instanceof AdminApplicationBulkDeleteInput);

        $repo = $this->em->getRepository(Application::class);

        foreach ($data->ids as $id) {
            $application = $repo->find($id);
            if ($application !== null) {
                $this->em->remove($application);
            }
        }

        $this->em->flush();
    }
}
