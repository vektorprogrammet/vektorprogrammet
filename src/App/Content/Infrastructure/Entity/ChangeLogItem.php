<?php

namespace App\Content\Infrastructure\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\Content\Infrastructure\Repository\ChangeLogItemRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * ChangeLogItem.
 */
#[ORM\Table(name: 'change_log_item')]
#[ORM\Entity(repositoryClass: ChangeLogItemRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(),
        new Get(),
    ],
    order: ['date' => 'DESC'],
    normalizationContext: ['groups' => ['changelog:read']],
)]
class ChangeLogItem
{
    /**
     * @var int
     */
    #[ORM\Column(name: 'id', type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[Groups(['changelog:read'])]
    private $id;

    /**
     * @var string
     */
    #[ORM\Column(name: 'title', type: 'string', nullable: false, length: 40)]
    #[Assert\Length(max: 40, maxMessage: 'Tittelen kan ikke være mer enn 40 tegn')]
    #[Groups(['changelog:read'])]
    private $title;

    /**
     * @var string
     */
    #[ORM\Column(name: 'description', type: 'string', length: 1000, nullable: true)]
    #[Groups(['changelog:read'])]
    private $description;

    /**
     * @var string
     */
    #[ORM\Column(name: 'githubLink', type: 'string', nullable: false, length: 1000)]
    #[Groups(['changelog:read'])]
    private $githubLink;

    /**
     * @var \DateTime
     */
    #[ORM\Column(name: 'date', type: 'datetime')]
    #[Groups(['changelog:read'])]
    private $date;

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
     * Set title.
     *
     * @param string $title
     *
     * @return ChangeLogItem
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set description.
     *
     * @param string $description
     *
     * @return ChangeLogItem
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set githubLink.
     *
     * @param string $githubLink
     *
     * @return ChangeLogItem
     */
    public function setGithubLink($githubLink)
    {
        $this->githubLink = $githubLink;

        return $this;
    }

    /**
     * Get githubLink.
     *
     * @return string
     */
    public function getGithubLink()
    {
        return $this->githubLink;
    }

    /**
     * Set date.
     *
     * @param \DateTime $date
     *
     * @return ChangeLogItem
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date.
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }
}
