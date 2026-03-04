<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Entity\Repository\ArticleRepository;
use App\State\ArticleProcessor;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ArticleRepository::class)]
#[ORM\Table(name: 'article')]
#[ORM\HasLifecycleCallbacks]
#[ApiResource(
    operations: [
        new GetCollection(),
        new Get(),
        new Post(
            security: "is_granted('ROLE_ADMIN')",
            processor: ArticleProcessor::class,
            denormalizationContext: ['groups' => ['article:write']],
        ),
        new Put(
            security: "is_granted('ROLE_ADMIN')",
            processor: ArticleProcessor::class,
            denormalizationContext: ['groups' => ['article:write']],
        ),
        new Delete(
            security: "is_granted('ROLE_ADMIN')",
        ),
    ],
    order: ['created' => 'DESC'],
    paginationItemsPerPage: 20,
    normalizationContext: ['groups' => ['article:read']],
)]
class Article
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[Groups(['article:read'])]
    protected $id;

    #[ORM\Column(type: 'string')]
    #[Assert\Length(max: 255)]
    #[Assert\NotBlank(message: 'Dette feltet kan ikke være tomt')]
    #[Groups(['article:read', 'article:write'])]
    protected $title;

    #[ORM\Column(type: 'string', unique: true)]
    #[Assert\Length(max: 255)]
    #[Groups(['article:read'])]
    protected $slug;

    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank(message: 'Dette feltet kan ikke være tomt')]
    #[Groups(['article:read', 'article:write'])]
    protected $article;

    #[ORM\Column(type: 'string')]
    #[Groups(['article:read', 'article:write'])]
    protected $imageLarge;

    #[ORM\Column(type: 'string')]
    #[Groups(['article:read', 'article:write'])]
    protected $imageSmall;

    #[ORM\Column(type: 'datetime')]
    #[Groups(['article:read'])]
    protected $created;

    #[ORM\Column(type: 'datetime')]
    #[Groups(['article:read'])]
    protected $updated;

    #[ORM\Column(type: 'boolean')]
    #[Groups(['article:read', 'article:write'])]
    protected $sticky;

    /**
     * @var bool
     */
    #[ORM\Column(type: 'boolean', nullable: true)]
    #[Groups(['article:read', 'article:write'])]
    private $published;
    #[ORM\ManyToMany(targetEntity: 'Department')]
    #[ORM\JoinTable(name: 'articles_departments')]
    #[ORM\JoinColumn(name: 'article_id', referencedColumnName: 'id')]
    #[ORM\InverseJoinColumn(name: 'department_id', referencedColumnName: 'id')]
    protected $departments; // Unidirectional, may change

    #[ORM\ManyToOne(targetEntity: 'User')]
    #[ORM\JoinColumn(name: 'author_id', referencedColumnName: 'id', onDelete: 'SET NULL')]
    protected $author; // Unidirectional, may change

    public function __construct()
    {
        $this->departments = new ArrayCollection();
        $this->setCreated(new \DateTime());
        $this->setUpdated(new \DateTime());
        $this->published = false;
        $this->sticky = false;
        $this->imageLarge = '';
        $this->imageSmall = '';
    }

    #[ORM\PreUpdate]
    public function setUpdatedValue()
    {
        $this->setUpdated(new \DateTime());
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
     * Set title.
     *
     * @param string $title
     *
     * @return Article
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
     * Set slug.
     *
     * @param string $slug
     *
     * @return Article
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug.
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set article.
     *
     * @param string $article
     *
     * @return Article
     */
    public function setArticle($article)
    {
        $this->article = $article;

        return $this;
    }

    /**
     * Get article.
     *
     * @return string
     */
    public function getArticle()
    {
        return $this->article;
    }

    /**
     * Set imageLarge.
     *
     * @param string $imageLarge
     *
     * @return Article
     */
    public function setImageLarge($imageLarge)
    {
        $this->imageLarge = $imageLarge;

        return $this;
    }

    /**
     * Get imageLarge.
     *
     * @return string
     */
    public function getImageLarge()
    {
        return $this->imageLarge;
    }

    /**
     * Set imageSmall.
     *
     * @param string $imageSmall
     *
     * @return Article
     */
    public function setImageSmall($imageSmall)
    {
        $this->imageSmall = $imageSmall;

        return $this;
    }

    /**
     * Get imageSmall.
     *
     * @return string
     */
    public function getImageSmall()
    {
        return $this->imageSmall;
    }

    /**
     * Set created.
     *
     * @param \DateTime $created
     *
     * @return Article
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created.
     *
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set updated.
     *
     * @param \DateTime $updated
     *
     * @return Article
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * Get updated.
     *
     * @return \DateTime
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * Add departments.
     *
     * @return Article
     */
    public function addDepartment(Department $departments)
    {
        $this->departments[] = $departments;

        return $this;
    }

    /**
     * Remove departments.
     */
    public function removeDepartment(Department $departments)
    {
        $this->departments->removeElement($departments);
    }

    /**
     * Get departments.
     *
     * @return Collection
     */
    public function getDepartments()
    {
        return $this->departments;
    }

    /**
     * Set author.
     *
     * @return Article
     */
    public function setAuthor(?User $author = null)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Get author.
     *
     * @return User
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Set sticky.
     *
     * @param bool $sticky
     *
     * @return Article
     */
    public function setSticky($sticky)
    {
        $this->sticky = $sticky;

        return $this;
    }

    /**
     * Get sticky.
     *
     * @return bool
     */
    public function getSticky()
    {
        return $this->sticky;
    }

    public function isPublished(): bool
    {
        return $this->published;
    }

    public function setPublished(bool $published): void
    {
        $this->published = $published;
    }
}
