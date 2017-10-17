<?php

namespace AppBundle\Controller;

use AppBundle\Entity\ImageGallery;
use AppBundle\Entity\Image;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ImageController extends Controller
{
    public function editImageAction(Image $image)
    {
        return $this->render('image_gallery/edit_image.html.twig', array(
            'image' => $image,
        ));
    }
}
