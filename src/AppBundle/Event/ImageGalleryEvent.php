<?php

namespace AppBundle\Event;

use AppBundle\Entity\ImageGallery;
use Symfony\Component\EventDispatcher\Event;

class ImageGalleryEvent extends Event
{
    const CREATED = 'imageGallery.created';
    const EDITED = 'imageGallery.edited';
    const DELETED = 'imageGallery.deleted';
    const IMAGE_ADDED = 'imageGallery.imageAdded';
    const IMAGE_REMOVED = 'imageGallery.imageRemoved';
    const IMAGE_EDITED = 'imageGallery.imageEdited';

    private $imageGallery;

    /**
     * ImageGalleryEvent constructor.
     *
     * @param ImageGallery $imageGallery
     */
    public function __construct(ImageGallery $imageGallery)
    {
        $this->imageGallery = $imageGallery;
    }

    /**
     * @return ImageGallery
     */
    public function getImageGallery(): ImageGallery
    {
        return $this->imageGallery;
    }
}
