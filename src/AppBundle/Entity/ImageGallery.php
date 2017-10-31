<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * ImageGallery
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\ImageGalleryRepository")
 */
class ImageGallery
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\OneToMany(targetEntity="Image", mappedBy="gallery", cascade={"remove"})
     */
    private $images;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     * @Assert\NotBlank(message="Bildegalleriet mangler tittel.")
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="referenceName", type="string", length=255)
     * @Assert\NotBlank(message="Bildegalleriet må ha et referansenavn.")
     */
    private $referenceName;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text")
     * @Assert\NotBlank(message="Bildegalleriet mangler beskrivelse.")
     */
    private $description;

    /**
     * @var ArrayCollection $filters
     *
     * @ORM\Column(name="filters", type="array")
     */
    private $filters;

    public function __construct()
    {
        $this->images = new ArrayCollection();
        $this->filters = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set images
     *
     * @param ArrayCollection $images
     * @return ImageGallery
     */
    public function setImages($images)
    {
        $this->images = $images;

        return $this;
    }

    /**
     * Get images
     *
     * @return ArrayCollection
     */
    public function getImages()
    {
        return $this->images;
    }

    /**
     * Add image
     *
     * @param Image $image
     * @return ImageGallery
     */
    public function addImage($image)
    {
        $image->setGallery($this);

        return $this;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return ImageGallery
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set referenceName
     *
     * @param string $referenceName
     * @return ImageGallery
     */
    public function setReferenceName($referenceName)
    {
        $this->referenceName = $referenceName;

        return $this;
    }

    /**
     * Get referenceName
     *
     * @return string
     */
    public function getReferenceName()
    {
        return $this->referenceName;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return ImageGallery
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }


    /**
     * @param array $filters
     */
    public function setFilters($filters)
    {
        $this->filters = new ArrayCollection($filters);
    }

    /**
     * @return array
     */
    public function getFilters()
    {
        return $this->filters->toArray();
    }

    /**
     * @param string $filter
     */
    public function addFilter($filter)
    {
        $this->filters->add($filter);
    }
}
