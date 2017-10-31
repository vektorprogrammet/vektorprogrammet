<?php

namespace AppBundle\Controller;

use AppBundle\Entity\ImageGallery;
use AppBundle\Entity\Image;
use AppBundle\Form\Type\ImageGalleryType;
use AppBundle\Form\Type\ImageType;
use AppBundle\Role\Roles;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Event\ImageGalleryEvent;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ImageGalleryController extends Controller
{
    public function createAction(Request $request)
    {
        $imageGallery = new ImageGallery();

        $filters = $this->get('liip_imagine.filter.configuration')->all();
        $form = $this->createForm(ImageGalleryType::class, $imageGallery);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userCanCreate = $this->get('app.roles')->userIsGranted($this->getUser(), Roles::TEAM_LEADER);
            if (!$userCanCreate) {
                throw new AccessDeniedException();
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($imageGallery);
            $em->flush();

            $this->get('event_dispatcher')->dispatch(ImageGalleryEvent::CREATED, new ImageGalleryEvent($imageGallery));

            return $this->redirectToRoute('image_gallery_edit', array('id' => $imageGallery->getId()));
        }

        $imageGalleries = $this->getDoctrine()->getRepository('AppBundle:ImageGallery')->findAll();

        return $this->render('image_gallery/show_overview.html.twig', array(
            'image_galleries' => $imageGalleries,
            'filters' => $filters,
            'form' => $form->createView(),
        ));
    }

    public function editAction(Request $request, ImageGallery $imageGallery)
    {
        $filters = $this->get('liip_imagine.filter.configuration')->all();
        $form = $this->createForm(ImageGalleryType::class, $imageGallery);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userCanCreate = $this->get('app.roles')->userIsGranted($this->getUser(), Roles::TEAM_LEADER);
            if (!$userCanCreate) {
                throw new AccessDeniedException();
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($imageGallery);
            $em->flush();

            $this->get('event_dispatcher')->dispatch(ImageGalleryEvent::EDITED, new ImageGalleryEvent($imageGallery));

            return $this->redirectToRoute('image_gallery_edit', array('id' => $imageGallery->getId()));
        }

        return $this->render('image_gallery/edit.html.twig', array(
            'image_gallery' => $imageGallery,
            'filters' => $filters,
            'form' => $form->createView(),
        ));
    }

    public function uploadImageAction(Request $request, ImageGallery $imageGallery)
    {
        $image = new Image();

        $form = $this->createForm(ImageType::class, $image);
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

    public function deleteAction(ImageGallery $imageGallery)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($imageGallery);
        $em->flush();

        $this->get('event_dispatcher')->dispatch(ImageGalleryEvent::DELETED, new ImageGalleryEvent($imageGallery));

        return $this->redirectToRoute('image_gallery_create');
    }

    public function editImageAction(Request $request, Image $image)
    {
        $oldPath = $image->getPath();
        $form = $this->createForm(ImageType::class, $image, array('upload_required' => false));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $image->setPath($oldPath);

            $em = $this->getDoctrine()->getManager();
            $em->persist($image);
            $em->flush();

            return $this->redirectToRoute('image_edit', array('id' => $image->getId()));
        }

        return $this->render('image_gallery/edit_image.html.twig', array(
            'form' => $form->createView(),
            'image' => $image,
        ));
    }

    public function deleteImageAction(Image $image)
    {
        $imageGallery = $image->getGallery();

        $em = $this->getDoctrine()->getManager();
        $em->remove($image);
        $em->flush();

        $this->get('event_dispatcher')->dispatch(ImageGalleryEvent::IMAGE_REMOVED, new ImageGalleryEvent($imageGallery));

        return $this->redirectToRoute('image_gallery_edit', array('id' => $imageGallery->getId()));
    }
}
