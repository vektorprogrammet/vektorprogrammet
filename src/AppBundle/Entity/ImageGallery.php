<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * ImageGallery
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\ImageGalleryRepository")
 * @UniqueEntity("referenceName")
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
     * @Assert\Length(min = 1, max = 255, maxMessage="Tittelen kan maks være 255 tegn."))
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="referenceName", type="string", length=255, unique=true)
     * @Assert\NotBlank(message="Bildegalleriet må ha et referansenavn.")
     * @Assert\Length(min = 1, max = 255, maxMessage="Referansenavnet kan maks være 255 tegn."))
     */
    private $referenceName;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text")
     * @Assert\NotBlank(message="Bildegalleriet mangler beskrivelse.")
     * @Assert\Length(min = 1, max = 5000, maxMessage="Beskrivelsen kan maks være 5000 tegn."))
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

	public function __toString() {
    	return $this->referenceName;
	}


}
