<?php

namespace App\Organization\Api\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Organization\Infrastructure\Repository\ExecutiveBoardRepository;
use Doctrine\ORM\EntityManagerInterface;

class AdminExecutiveBoardEditProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly ExecutiveBoardRepository $executiveBoardRepo,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): array
    {
        $board = $this->executiveBoardRepo->findBoard();

        $board->setName($data->name);
        if ($data->description !== null) {
            $board->setDescription($data->description);
        }
        if ($data->shortDescription !== null) {
            $board->setShortDescription($data->shortDescription);
        }

        $this->em->persist($board);
        $this->em->flush();

        return ['id' => $board->getId(), 'name' => $board->getName()];
    }
}
