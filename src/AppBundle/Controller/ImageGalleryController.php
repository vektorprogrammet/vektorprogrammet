<?php

namespace AppBundle\Controller;

use AppBundle\Entity\ImageGallery;
use AppBundle\Entity\Image;
use AppBundle\Form\Type\ImageGalleryType;
use AppBundle\Form\Type\ImageType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ImageGalleryController extends Controller
{
    public function createAction(Request $request)
    {
        $imageGallery = new ImageGallery();

        $form = $this->createForm(ImageGalleryType::class, $imageGallery);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($imageGallery);
            $em->flush();

            //$this->get('event_dispatcher')->dispatch(ImageGallery::CREATED, new ImageGalleryEvent($imageGallery));

            return $this->redirectToRoute('image_gallery_edit', array('id' => $imageGallery->getId()));
        }

        $imageGalleries = $this->getDoctrine()->getRepository('AppBundle:ImageGallery')->findAll();

        return $this->render('image_gallery/show_overview.html.twig', array(
            'image_galleries' => $imageGalleries,
            'form' => $form->createView(),
        ));
    }

    public function editAction(Request $request, ImageGallery $imageGallery)
    {
        $form = $this->createForm(ImageGalleryType::class, $imageGallery);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($imageGallery);
            $em->flush();

            //$this->get('event_dispatcher')->dispatch(ImageGallery::EDITED, new ImageGalleryEvent($imageGallery));

            return $this->redirectToRoute('image_gallery_edit', array('id' => $imageGallery->getId()));
        }

        return $this->render('image_gallery/edit.html.twig', array(
            'image_gallery' => $imageGallery,
            'form' => $form->createView(),
        ));
    }

    public function uploadImageAction(Request $request, ImageGallery $imageGallery)
    {
        $image = new Image();

        $form = $this->createForm(ImageType::class, $image);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $path = $this->get('app.file_uploader')->uploadGalleryImage($request);
            $image->setPath($path);

            $imageGallery->addImage($image);

            $em = $this->getDoctrine()->getManager();
            $em->persist($image);
            $em->persist($imageGallery);
            $em->flush();

            //$this->get('event_dispatcher')->dispatch(ImageGallery::CREATED, new ImageGalleryEvent($imageGallery));

            return $this->redirectToRoute('image_gallery_edit', array('id' => $imageGallery->getId()));
        }
        return $this->render('image_gallery/upload_image.html.twig', array(
            'image_gallery' => $imageGallery,
            'form' => $form->createView(),
        ));
    }

    public function editImageAction(Request $request, Image $image)
    {
        $oldPath = $image->getPath();
        $form = $this->createForm(ImageType::class, $image);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $image->setPath($oldPath);

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
