<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * StaticContent.
 *
 * @ORM\Entity
 * @ORM\Table(name="static_content")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\StaticContentRepository")
 */
class StaticContent
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="html_id", type="string", length=50)
     */
    private $htmlId;

    /**
     * @var string
     *
     * @ORM\Column(name="html", type="text")
     */
    private $html;

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set htmlId.
     *
     * @param string $htmlId
     *
     * @return Static_content
     */
    public function setHtmlId($htmlId)
    {
        $this->htmlId = $htmlId;

        return $this;
    }

    /**
     * Get htmlId.
     *
     * @return string
     */
    public function getHtmlId()
    {
        return $this->htmlId;
    }

    /**
     * Set html.
     *
     * @param string $html
     *
     * @return Static_content
     */
    public function setHtml($html)
    {
        $this->html = $html;

        return $this;
    }

    /**
     * Get html.
     *
     * @return string
     */
    public function getHtml()
    {
        return $this->html;
    }
}
