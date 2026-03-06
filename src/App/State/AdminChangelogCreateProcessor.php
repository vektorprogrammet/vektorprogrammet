<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\ChangeLogItem;
use Doctrine\ORM\EntityManagerInterface;

class AdminChangelogCreateProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): array
    {
        $item = new ChangeLogItem();
        $item->setTitle($data->title);
        $item->setDescription($data->description ?? '');
        $item->setGithubLink($data->githubLink ?? '');

        if ($data->date !== null) {
            $item->setDate(new \DateTime($data->date));
        } else {
            $item->setDate(new \DateTime());
        }

        $this->em->persist($item);
        $this->em->flush();

        return ['id' => $item->getId()];
    }
}
