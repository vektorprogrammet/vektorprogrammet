<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

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
     * @ORM\ManyToMany(targetEntity="Image", inversedBy="galleries")
     */
    private $images;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="referenceName", type="string", length=255)
     */
    private $referenceName;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text")
     */
    private $description;

    public function __construct()
    {
        $this->images = new ArrayCollection();
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
        $this->images->add($image);

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
}
