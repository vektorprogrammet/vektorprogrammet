<?php

namespace App\Content\Infrastructure\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\Content\Infrastructure\Repository\StaticContentRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

/**
 * StaticContent.
 */
#[ORM\Table(name: 'static_content')]
#[ORM\Entity(repositoryClass: StaticContentRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(),
        new Get(),
    ],
    normalizationContext: ['groups' => ['static_content:read']],
)]
class StaticContent implements \Stringable
{
    /**
     * @var int
     */
    #[ORM\Column(name: 'id', type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[Groups(['static_content:read'])]
    private $id;

    /**
     * @var string
     */
    #[ORM\Column(name: 'html_id', type: 'string', length: 50)]
    #[Groups(['static_content:read'])]
    private $htmlId;

    /**
     * @var string
     */
    #[ORM\Column(name: 'html', type: 'text')]
    #[Groups(['static_content:read'])]
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
     * @return StaticContent
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
     * @return StaticContent
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

    public function __toString(): string
    {
        return $this->htmlId;
    }
}
