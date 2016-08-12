<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="photo")
 */
class Photo
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $dateAdded;

    /**
     * @ORM\Column(type="datetime", nullable=true))
     */
    protected $dateTaken;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     **/
    protected $addedByUser;

    /**
     * @ORM\Column(type="string", length=255, nullable=true))
     */
    protected $comment;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $pathToFile;

    /**
     * @ORM\ManyToOne(targetEntity="Gallery", inversedBy="photos")
     * @ORM\JoinColumn(name="gallery_id", referencedColumnName="id")
     */
    protected $gallery;

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
     * Set path.
     *
     * @param string $path
     *
     * @return Photo
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get path.
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set public.
     *
     * @param bool $public
     *
     * @return Photo
     */
    public function setPublic($public)
    {
        $this->public = $public;

        return $this;
    }

    /**
     * Get public.
     *
     * @return bool
     */
    public function getPublic()
    {
        return $this->public;
    }

    /**
     * Set comment.
     *
     * @param string $comment
     *
     * @return Photo
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get comment.
     *
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Set added.
     *
     * @param \DateTime $added
     *
     * @return Photo
     */
    public function setAdded($added)
    {
        $this->added = $added;

        return $this;
    }

    /**
     * Get added.
     *
     * @return \DateTime
     */
    public function getAdded()
    {
        return $this->added;
    }

    /**
     * Set addedByUser.
     *
     * @param int $addedByUser
     *
     * @return Photo
     */
    public function setAddedByUser($addedByUser)
    {
        $this->addedByUser = $addedByUser;

        return $this;
    }

    /**
     * Get addedByUser.
     *
     * @return int
     */
    public function getAddedByUser()
    {
        return $this->addedByUser;
    }

    /**
     * Set dateAdded.
     *
     * @param \DateTime $dateAdded
     *
     * @return Photo
     */
    public function setDateAdded($dateAdded)
    {
        $this->dateAdded = $dateAdded;

        return $this;
    }

    /**
     * Get dateAdded.
     *
     * @return \DateTime
     */
    public function getDateAdded()
    {
        return $this->dateAdded;
    }

    /**
     * Set pathToFile.
     *
     * @param string $pathToFile
     *
     * @return Photo
     */
    public function setPathToFile($pathToFile)
    {
        $this->pathToFile = $pathToFile;

        return $this;
    }

    /**
     * Get pathToFile.
     *
     * @return string
     */
    public function getPathToFile()
    {
        return $this->pathToFile;
    }

    /**
     * Set gallery.
     *
     * @param \AppBundle\Entity\Gallery $gallery
     *
     * @return Photo
     */
    public function setGallery(\AppBundle\Entity\Gallery $gallery = null)
    {
        $this->gallery = $gallery;

        return $this;
    }

    /**
     * Get gallery.
     *
     * @return \AppBundle\Entity\Gallery
     */
    public function getGallery()
    {
        return $this->gallery;
    }

    /**
     * Set dateTaken.
     *
     * @param \DateTime $dateTaken
     *
     * @return Photo
     */
    public function setDateTaken($dateTaken)
    {
        $this->dateTaken = $dateTaken;

        return $this;
    }

    /**
     * Get dateTaken.
     *
     * @return \DateTime
     */
    public function getDateTaken()
    {
        return $this->dateTaken;
    }
}
