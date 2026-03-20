<?php

namespace App\Content\Infrastructure\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\Table(name: 'sponsor')]
#[ApiResource(
    operations: [
        new GetCollection(),
        new Get(),
    ],
    normalizationContext: ['groups' => ['sponsor:read']],
)]
class Sponsor
{
    #[ORM\Column(type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[Groups(['sponsor:read'])]
    protected $id;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    #[Assert\NotBlank(message: 'Feletet kan ikke være tomt.')]
    #[Groups(['sponsor:read'])]
    protected $name;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Assert\NotBlank(message: 'Feletet kan ikke være tomt.')]
    #[Groups(['sponsor:read'])]
    protected $url;

    /**
     * Available sizes: "small", "medium" and "large".
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Assert\NotBlank(message: 'Feletet kan ikke være tomt.')]
    #[Groups(['sponsor:read'])]
    protected $size;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['sponsor:read'])]
    protected $logoImagePath;

    /**
     * Sponsor constructor.
     */
    public function __construct()
    {
        $this->size = 'medium';
    }

    /**
     * @return string
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @param string $size
     */
    public function setSize($size)
    {
        $this->size = $size;
    }

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
     * Set name.
     *
     * @param string $name
     *
     * @return Sponsor
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set url.
     *
     * @param string $url
     *
     * @return Sponsor
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url.
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set logoImagePath.
     *
     * @param string $logoImagePath
     *
     * @return Sponsor
     */
    public function setLogoImagePath($logoImagePath)
    {
        $this->logoImagePath = $logoImagePath;

        return $this;
    }

    /**
     * Get logoImagePath.
     *
     * @return string
     */
    public function getLogoImagePath()
    {
        return $this->logoImagePath;
    }
}
