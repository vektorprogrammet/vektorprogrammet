<?php

namespace AppBundle\EventSubscriber;

use AppBundle\Event\ImageGalleryEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Session\Session;

class ImageGallerySubscriber implements EventSubscriberInterface
{
    private $logger;
    private $session;

    public function __construct(LoggerInterface $logger, Session $session)
    {
        $this->logger = $logger;
        $this->session = $session;
    }

    public static function getSubscribedEvents()
    {
        return array(
            ImageGalleryEvent::CREATED => array(
                array('logCreatedEvent', 1),
                array('addCreatedFlashMessage', 1),
            ),
            ImageGalleryEvent::DELETED => array(
                array('logDeletedEvent', 1),
                array('addDeletedFlashMessage', 1),
            ),
            ImageGalleryEvent::EDITED => array(
                array('logEditedEvent', 1),
                array('addEditedFlashMessage', 1),
            ),
            ImageGalleryEvent::IMAGE_ADDED => array(
                array('addImageAddedFlashMessage', 1),
            ),
            ImageGalleryEvent::IMAGE_REMOVED => array(
                array('addImageRemovedFlashMessage', 1),
            ),
            ImageGalleryEvent::IMAGE_EDITED => array(
                array('addImageEditedFlashMessage', 1),
            ),
        );
    }

    public function logCreatedEvent(ImageGalleryEvent $event)
    {
        $imageGallery = $event->getImageGallery();
        $this->logger->info('New image gallery *'. $imageGallery->getTitle() . '* on endpoint /api/image_gallery/' . $imageGallery->getReferenceName(). '.');
    }

    public function addCreatedFlashMessage(ImageGalleryEvent $event)
    {
        $imageGallery = $event->getImageGallery();
        $this->session->getFlashBag()->add('success', 'Bildegalleriet er opprettet på endpointet /api/image_gallery/' . $imageGallery->getReferenceName() . '.');
    }

    public function logDeletedEvent(ImageGalleryEvent $event)
    {
        $imageGallery = $event->getImageGallery();
        $this->logger->info('Image gallery *'. $imageGallery->getTitle() . '* on endpoint /api/image_gallery/' . $imageGallery->getReferenceName(). ' has been deleted.');
    }

    public function addDeletedFlashMessage(ImageGalleryEvent $event)
    {
        $imageGallery = $event->getImageGallery();
        $this->session->getFlashBag()->add('success', 'Bildegalleriet på endpointet /api/image_gallery/' . $imageGallery->getReferenceName() . ' har blitt slettet.');
    }

    public function logEditedEvent(ImageGalleryEvent $event)
    {
        $imageGallery = $event->getImageGallery();
        $this->logger->info('Image gallery *'. $imageGallery->getTitle() . '* on endpoint /api/image_gallery/' . $imageGallery->getReferenceName(). ' was edited.');
    }

    public function addEditedFlashMessage()
    {
        $this->session->getFlashBag()->add('success', 'Endringen har blitt lagret.');
    }

    public function addImageAddedFlashMessage()
    {
        $this->session->getFlashBag()->add('success', 'Bildet har blitt lagt til.');
    }

    public function addImageRemovedFlashMessage()
    {
        $this->session->getFlashBag()->add('success', 'Bildet ble slettet.');
    }

    public function addImageEditedFlashMessage()
    {
        $this->session->getFlashBag()->add('success', 'Beskrivelsen har blitt endret.');
    }
}
