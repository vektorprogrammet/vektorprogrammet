<?php
/**
 * Created by PhpStorm.
 * User: Tommy
 * Date: 24.04.2015
 * Time: 01:52.
 */

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

//todo: change name to Album.php. Table name should also be changed to album.

/**
 * @ORM\Table(name="gallery")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\GalleryRepository")
 */
class Gallery
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $name;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $dateCreated;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     **/
    protected $createdByUser;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $private;

    /**
     * @ORM\OneToMany(targetEntity="Photo", mappedBy="gallery", cascade={"remove"})
     **/
    protected $photos;

    public function __construct()
    {
        $this->photos = new ArrayCollection();
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
     * @return Gallery
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
     * Set dateCreated.
     *
     * @param \DateTime $dateCreated
     *
     * @return Gallery
     */
    public function setDateCreated($dateCreated)
    {
        $this->dateCreated = $dateCreated;

        return $this;
    }

    /**
     * Get dateCreated.
     *
     * @return \DateTime
     */
    public function getDateCreated()
    {
        return $this->dateCreated;
    }

    /**
     * Set createdByUser.
     *
     * @param \AppBundle\Entity\User $createdByUser
     *
     * @return Gallery
     */
    public function setCreatedByUser(\AppBundle\Entity\User $createdByUser = null)
    {
        $this->createdByUser = $createdByUser;

        return $this;
    }

    /**
     * Get createdByUser.
     *
     * @return \AppBundle\Entity\User
     */
    public function getCreatedByUser()
    {
        return $this->createdByUser;
    }

    /**
     * Add photos.
     *
     * @param \AppBundle\Entity\Photo $photos
     *
     * @return Gallery
     */
    public function addPhoto(\AppBundle\Entity\Photo $photos)
    {
        $this->photos[] = $photos;

        return $this;
    }

    /**
     * Remove photos.
     *
     * @param \AppBundle\Entity\Photo $photos
     */
    public function removePhoto(\AppBundle\Entity\Photo $photos)
    {
        $this->photos->removeElement($photos);
    }

    /**
     * Get photos.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPhotos()
    {
        return $this->photos;
    }

    /**
     * Set private.
     *
     * @param bool $private
     *
     * @return Gallery
     */
    public function setPrivate($private)
    {
        $this->private = $private;

        return $this;
    }

    /**
     * Get private.
     *
     * @return bool
     */
    public function getPrivate()
    {
        return $this->private;
    }
}
