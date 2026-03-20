<?php

namespace App\Content\Twig;

use App\Content\Infrastructure\Entity\StaticContent;
use Doctrine\ORM\EntityManagerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class StaticContentExtension extends AbstractExtension
{
    public function __construct(private readonly EntityManagerInterface $em)
    {
    }

    public function getName()
    {
        return 'Static_contentExtension';
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('get_content', $this->getContent(...)),
        ];
    }

    public function getContent($htmlId)
    {
        if (!$htmlId) {
            return '';
        }

        $content = $this->em
            ->getRepository(StaticContent::class)
            ->findOneByHtmlId($htmlId);
        if (!$content) {
            $content = new StaticContent();
            $content->setHtmlId($htmlId);
            $content->setHtml('Dette er en ny statisk tekst for: '.$htmlId);

            $this->em->persist($content);
            $this->em->flush();
        }

        return $content->getHtml();
    }
}
