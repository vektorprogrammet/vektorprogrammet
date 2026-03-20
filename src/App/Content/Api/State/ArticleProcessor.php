<?php

namespace App\Content\Api\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Post;
use ApiPlatform\State\ProcessorInterface;
use App\Content\Infrastructure\Entity\Article;
use App\Content\Infrastructure\Repository\ArticleRepository;
use App\Identity\Infrastructure\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\String\Slugger\SluggerInterface;

class ArticleProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly Security $security,
        private readonly SluggerInterface $slugger,
        private readonly ArticleRepository $articleRepo,
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

        if ($data->getTitle() !== '' && $data->getTitle() !== null) {
            $slug = $this->slugger->slug($data->getTitle())->lower()->toString();
            $existingSlugs = $this->articleRepo->findSlugs();
            $base = $slug;
            $i = 2;
            while (in_array($slug, $existingSlugs, true)) {
                $slug = $base.'-'.$i;
                ++$i;
            }
            $data->setSlug($slug);
        }

        $this->em->persist($data);
        $this->em->flush();

        return $data;
    }
}
