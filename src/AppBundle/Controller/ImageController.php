<?php

namespace AppBundle\Controller;

use AppBundle\Entity\ImageGallery;
use AppBundle\Entity\Image;
use AppBundle\Form\Type\ImageType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ImageController extends Controller
{
    public function editImageAction(Request $request, Image $image)
    {
        $form = $this->createForm(ImageType::class, $image);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($image);
            $em->flush();

            //$this->get('event_dispatcher')->dispatch(ImageEvent::EDITED, new ImageEvent($image));

            return $this->redirectToRoute('image_edit', array('id' => $image->getId()));
        }

        return $this->render('image_gallery/edit_image.html.twig', array(
            'form' => $form->createView(),
            'image' => $image,
        ));
    }
}
