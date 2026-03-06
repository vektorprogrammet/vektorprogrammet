<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\ChangeLogItem;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AdminChangelogEditProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): array
    {
        $id = $uriVariables['id'] ?? null;
        $item = $this->em->getRepository(ChangeLogItem::class)->find($id);

        if ($item === null) {
            throw new NotFoundHttpException('Changelog item not found.');
        }

        $item->setTitle($data->title);

        if ($data->description !== null) {
            $item->setDescription($data->description);
        }
        if ($data->date !== null) {
            $item->setDate(new \DateTime($data->date));
        }
        if ($data->githubLink !== null) {
            $item->setGithubLink($data->githubLink);
        }

        $this->em->persist($item);
        $this->em->flush();

        return [
            'id' => $item->getId(),
            'title' => $item->getTitle(),
        ];
    }
}
