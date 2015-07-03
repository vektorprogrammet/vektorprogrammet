<?php
/**
 * Created by PhpStorm.
 * User: Tommy
 * Date: 17.03.2015
 * Time: 08:16
 *
 * Read this for documentaion:
 * http://cristian-radulescu.ro/article/doctrine-entities-in-twig-templates.html
 */

namespace AppBundle\Twig\Extension;
use AppBundle\Entity\StaticContent;

class StaticContentExtension extends \Twig_Extension {
    protected $doctrine;
    protected $securityContext;

    public function __construct($doctrine, $securityContext){
        $this->doctrine = $doctrine;
        $this->securityContext = $securityContext;
    }

    public function getName(){
        return "Static_contentExtension";
    }

    public function getFunctions(){
        return array(
            'get_content' => new \Twig_Function_Method($this, 'getContent'),
            'element_editable' => new \Twig_Function_Method($this, 'elementEditable')
        );
    }

    public function getContent($html_element_id){
        $content = $this->doctrine
                    ->getEntityManager()
                    ->getRepository('AppBundle:StaticContent')
                    ->findOneByHtmlId($html_element_id);
        if (!$content) {
           return "No content found!";
        }
        return $content->getHtml();
    }

    /**
     * Returns 'editable' if current user is allowed to edit static content,
     * else returns empty string
     */
    public function elementEditable(){
        if ($this->securityContext->isGranted('ROLE_ADMIN')) {
            return "editable";
        } else {
            return "";
        }
    }
}