<?php

namespace App\Content\Api\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Content\Infrastructure\Entity\StaticContent;
use App\Content\Infrastructure\Repository\StaticContentRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class StaticContentByHtmlIdProvider implements ProviderInterface
{
    public function __construct(
        private readonly StaticContentRepository $repository,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): StaticContent
    {
        $htmlId = $uriVariables['htmlId'] ?? null;
        $content = $htmlId !== null ? $this->repository->findOneByHtmlId((string) $htmlId) : null;

        if ($content === null) {
            throw new NotFoundHttpException('Static content not found.');
        }

        return $content;
    }
}
