<?php


namespace AppBundle\Service;

use AppBundle\Entity\Article;
use Doctrine\ORM\EntityManagerInterface;

class SlugMaker
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function setSlugFor(Article $article)
    {
        $slugs = $this->em->getRepository('AppBundle:Article')->findSlugs();

        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $this->replaceCharacters($article->getTitle()))));
        $i = 2;
        while (array_search($slug, $slugs) !== false) {
            $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $this->replaceCharacters($article->getTitle())))) . '-' .$i;
            $i++;
        }

        $article->setSlug($slug);
        return $slug;
    }

    private function replaceCharacters($string)
    {
        $a = array('Æ', 'Ø', 'Å', 'æ', 'ø', 'å', '&shy;', '-', '!', ',', '.');
        $b = array('AE', 'O', 'A', 'ae', 'o', 'a', '', '', '', '', '', '');
        $newString = str_replace($a, $b, $string);

        return $newString;
    }
}
