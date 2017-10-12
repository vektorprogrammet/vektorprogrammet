<?php

namespace AppBundle\Controller;

use AppBundle\Entity\ImageGallery;
use AppBundle\Entity\Image;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ImageGalleryController extends Controller
{
    public function showOverviewAction()
    {
        $imageGalleries = $this->getDoctrine()->getRepository('AppBundle:ImageGallery')->findAll();
        dump($imageGalleries);

        return $this->render('image_gallery/show_overview.html.twig', array(
            'image_galleries' => $imageGalleries,
        ));
    }

    public function showIndividualAction(ImageGallery $imageGallery)
    {
        return $this->render('image_gallery/show_individual.html.twig', array(
            'image_gallery' => $imageGallery,
        ));
    }
}
