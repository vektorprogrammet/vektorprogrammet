<?php

namespace AppBundle\Controller;

use AppBundle\Entity\ImageGallery;
use AppBundle\Entity\Image;
use AppBundle\Form\Type\EditImageType;
use AppBundle\Form\Type\ImageGalleryType;
use AppBundle\Form\Type\UploadImageType;
use AppBundle\Role\Roles;
use Doctrine\ORM\NoResultException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Event\ImageGalleryEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ImageGalleryController extends Controller
{
    public function createAction(Request $request)
    {
        $imageGallery = new ImageGallery();

        $allFilters = array();
        foreach ($this->get('liip_imagine.filter.configuration')->all() as $filterName => $filter) {
            $allFilters[$filterName] = $filterName;
        }

        $form = $this->createForm(ImageGalleryType::class, $imageGallery, array('all_filters' => $allFilters));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userCanCreate = $this->get('app.roles')->userIsGranted($this->getUser(), Roles::TEAM_LEADER);
            if (!$userCanCreate) {
                throw new AccessDeniedException();
            }

            $imageGallery->setReferenceName(urlencode($imageGallery->getReferenceName()));

            $em = $this->getDoctrine()->getManager();
            $em->persist($imageGallery);
            $em->flush();

            $this->get('event_dispatcher')->dispatch(ImageGalleryEvent::CREATED, new ImageGalleryEvent($imageGallery));

            return $this->redirectToRoute('image_gallery_edit', array('id' => $imageGallery->getId()));
        }

        $imageGalleries = $this->getDoctrine()->getRepository('AppBundle:ImageGallery')->findAll();

        return $this->render('image_gallery/show_overview.html.twig', array(
            'image_galleries' => $imageGalleries,
            'filters' => $allFilters,
            'form' => $form->createView(),
        ));
    }

    public function editAction(Request $request, ImageGallery $imageGallery)
    {
        $allFilters = array();
        foreach ($this->get('liip_imagine.filter.configuration')->all() as $filterName => $filter) {
            $allFilters[$filterName] = $filterName;
        }

        $form = $this->createForm(ImageGalleryType::class, $imageGallery, array('all_filters' => $allFilters));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userCanCreate = $this->get('app.roles')->userIsGranted($this->getUser(), Roles::TEAM_LEADER);
            if (!$userCanCreate) {
                throw new AccessDeniedException();
            }

            $imageGallery->setReferenceName(urlencode($imageGallery->getReferenceName()));

            $em = $this->getDoctrine()->getManager();
            $em->persist($imageGallery);
            $em->flush();

            $this->get('event_dispatcher')->dispatch(ImageGalleryEvent::EDITED, new ImageGalleryEvent($imageGallery));

            return $this->redirectToRoute('image_gallery_edit', array('id' => $imageGallery->getId()));
        }

        return $this->render('image_gallery/edit.html.twig', array(
            'image_gallery' => $imageGallery,
            'filters' => $allFilters,
            'form' => $form->createView(),
        ));
    }

    public function uploadImageAction(Request $request, ImageGallery $imageGallery)
    {
        $image = new Image();

        $form = $this->createForm(UploadImageType::class, $image);
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
        $form = $this->createForm(EditImageType::class, $image);
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

    public function getAction($referenceName)
    {
        try {
            $imageGallery = $this->getDoctrine()->getRepository('AppBundle:ImageGallery')->findByReferenceName($referenceName);
        } catch(NoResultException $exception) {
            throw new NotFoundHttpException('No image gallery exists with reference name ' . $referenceName . '.');
        }

        $imagineCacheManager = $this->get('liip_imagine.cache.manager');

        $imageEntities = $imageGallery->getImages();
        $images = array();
        foreach ($imageEntities as $imageEntity) {
            $image = array();
            $paths = array();
            $filters = $imageGallery->getFilters();

            foreach ($filters as $filter) {
                $paths[$filter] = $imagineCacheManager->getBrowserPath($imageEntity->getPath(), $filter);
            }

            $image['description'] = $imageEntity->getDescription();
            $image['paths'] = $paths;

            array_push($images, $image);
        }

        return new JsonResponse($images);
    }
}
