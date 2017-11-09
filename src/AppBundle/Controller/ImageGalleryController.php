<?php

namespace AppBundle\Controller;

use AppBundle\Entity\ImageGallery;
use AppBundle\Form\Type\ImageGalleryType;
use AppBundle\Role\Roles;
use Doctrine\ORM\NoResultException;
use Liip\ImagineBundle\Exception\Imagine\Filter\NonExistingFilterException;
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

        $form = $this->createForm(ImageGalleryType::class, $imageGallery);
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
            'form' => $form->createView(),
        ));
    }

    public function editAction(Request $request, ImageGallery $imageGallery)
    {
        $form = $this->createForm(ImageGalleryType::class, $imageGallery);
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

    public function getAction(Request $request, $referenceName)
    {
        $filter = $request->query->get('filter');

        try {
            $imageGallery = $this->getDoctrine()->getRepository('AppBundle:ImageGallery')->findByReferenceName($referenceName);
        } catch (NoResultException $exception) {
            throw new NotFoundHttpException('No image gallery exists with reference name ' . $referenceName . '.');
        }

        $imagineCacheManager = $this->get('liip_imagine.cache.manager');

        $imageEntities = $imageGallery->getImages();
        $images = array();
        foreach ($imageEntities as $imageEntity) {
            $image = array();
            $image['description'] = $imageEntity->getDescription();

            try {
                $image['path'] = $imagineCacheManager->getBrowserPath($imageEntity->getPath(), $filter);
            } catch (NonExistingFilterException $exception) {
                $image['path'] = $imageEntity->getPath();
            }

            array_push($images, $image);
        }

        return new JsonResponse($images);
    }
}
