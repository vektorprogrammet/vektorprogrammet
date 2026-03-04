<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Post;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Article;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

class ArticleProcessor implements ProcessorInterface
{
    public function __construct(
        private EntityManagerInterface $em,
        private Security $security,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): Article
    {
        assert($data instanceof Article);

        if ($operation instanceof Post) {
            /** @var User $user */
            $user = $this->security->getUser();
            $data->setAuthor($user);
        }

        // Auto-generate slug from title
        if ($data->getTitle()) {
            $slug = $this->generateSlug($data->getTitle());
            $data->setSlug($slug);
        }

        $this->em->persist($data);
        $this->em->flush();

        return $data;
    }

    private function generateSlug(string $title): string
    {
        $slug = mb_strtolower($title);
        $slug = preg_replace('/[^a-z0-9\s-]/', '', $slug);
        $slug = preg_replace('/[\s-]+/', '-', $slug);

        return trim($slug, '-');
    }
}
