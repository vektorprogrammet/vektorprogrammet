<?php

namespace App\Support\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class SafeHtmlExtension extends AbstractExtension
{
    private $blacklistedTags = ['script', 'iframe'];

    public function getFilters()
    {
        return [
            new TwigFilter('safe_html', $this->htmlFilter(...), [
                'is_safe' => ['html'],
            ]),
        ];
    }

    public function htmlFilter($html)
    {
        foreach ($this->blacklistedTags as $tag) {
            $html = preg_replace('/<'.$tag.'\b[^<]*(?:(?!<\/'.$tag.'>)<[^<]*)*<\/'.$tag.'>/i', '', (string) $html);
            $html = str_replace('<'.$tag, '', $html);
        }

        return $html;
    }

    public function getName()
    {
        return 'safe_html_extension';
    }
}
