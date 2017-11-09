<?php

namespace AppBundle\Controller;

use AppBundle\Entity\ImageGallery;
use AppBundle\Entity\Image;
use AppBundle\Form\Type\EditImageType;
use AppBundle\Form\Type\UploadImageType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Event\ImageGalleryEvent;

class ImageController extends Controller
{
    public function uploadAction(Request $request, ImageGallery $imageGallery)
    {
        $image = new Image();

        $form = $this->createForm(UploadImageType::class, $image, array('validation_groups' => array('image_gallery_upload')));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $image->setGallery($imageGallery);

            $em = $this->getDoctrine()->getManager();
            $em->persist($image);
            $em->flush();

            $this->get('event_dispatcher')->dispatch(ImageGalleryEvent::IMAGE_ADDED, new ImageGalleryEvent($imageGallery));

            return $this->redirectToRoute('image_gallery_edit', array('id' => $imageGallery->getId()));
        }
        return $this->render('image_gallery/upload_image.html.twig', array(
            'image_gallery' => $imageGallery,
            'form' => $form->createView(),
        ));
    }

    public function editAction(Request $request, Image $image)
    {
        $oldPath = $image->getPath();
        $form = $this->createForm(EditImageType::class, $image);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $image->setPath($oldPath);

            $em = $this->getDoctrine()->getManager();
            $em->persist($image);
            $em->flush();

            $imageGallery = $image->getGallery();
            $this->get('event_dispatcher')->dispatch(ImageGalleryEvent::IMAGE_EDITED, new ImageGalleryEvent($imageGallery));

            return $this->redirectToRoute('image_edit', array('id' => $image->getId()));
        }

        return $this->render('image_gallery/edit_image.html.twig', array(
            'form' => $form->createView(),
            'image' => $image,
        ));
    }

    public function deleteAction(Image $image)
    {
        $imageGallery = $image->getGallery();

        $em = $this->getDoctrine()->getManager();
        $em->remove($image);
        $em->flush();

        $this->get('event_dispatcher')->dispatch(ImageGalleryEvent::IMAGE_REMOVED, new ImageGalleryEvent($imageGallery));

        return $this->redirectToRoute('image_gallery_edit', array('id' => $imageGallery->getId()));
    }
}
