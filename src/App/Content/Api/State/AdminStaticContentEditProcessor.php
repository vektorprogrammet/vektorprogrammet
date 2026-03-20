<?php

namespace App\Content\Api\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Content\Infrastructure\Entity\StaticContent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AdminStaticContentEditProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): array
    {
        $id = $uriVariables['id'] ?? null;
        $content = $this->em->getRepository(StaticContent::class)->find($id);

        if ($content === null) {
            throw new NotFoundHttpException('Static content not found.');
        }

        if ($data->html !== null) {
            $content->setHtml($data->html);
        }

        $this->em->persist($content);
        $this->em->flush();

        return [
            'id' => $content->getId(),
            'htmlId' => $content->getHtmlId(),
            'html' => $content->getHtml(),
        ];
    }
}
