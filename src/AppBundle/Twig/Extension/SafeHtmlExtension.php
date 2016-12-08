<?php

namespace AppBundle\Twig\Extension;

class SafeHtmlExtension extends \Twig_Extension
{
    private $blacklistedTags = ['script', 'iframe'];

    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('safe_html', array($this, 'htmlFilter'), array(
                'is_safe' => array('html'),
            )),
        );
    }

    public function htmlFilter($html)
    {
        foreach ($this->blacklistedTags as $tag) {
            $html = preg_replace('/<'.$tag.'\b[^<]*(?:(?!<\/'.$tag.'>)<[^<]*)*<\/'.$tag.'>/i', '', $html);
            $html = str_replace('<'.$tag, '', $html);
        }

        return $html;
    }

    public function getName()
    {
        return 'safe_html_extension';
    }
}
