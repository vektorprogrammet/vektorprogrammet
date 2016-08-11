<?php

/**
 * Created by PhpStorm.
 * User: Tommy
 * Date: 24.04.2015
 * Time: 19:41.
 */

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Gallery;
use AppBundle\Entity\Photo;
use AppBundle\FileSystem\FileUploader;

class GalleryController extends Controller
{
    //File types allowed in gallery.
    public $allowed_file_types = ['image/gif', 'image/jpeg', 'image/png', 'image/bmp'];

    /**
     * Opens the gallery page.
     *
     * @param $id
     *
     * @return Response
     */
    public function showAction($id)
    {
        return $this->render('gallery/gallery.html.twig', array('id' => $id));
    }

    /**
     * Deletes a single photo.
     *
     * @param $photo_id
     *
     * @return Response
     */
    public function deletePhotoAction($photo_id)
    {
        $photo = $this->getDoctrine()
                ->getRepository('AppBundle:Photo')
                ->find($photo_id);
        $em = $this->getDoctrine()->getEntityManager();
        $em->remove($photo);
        $em->flush();
        //todo: remember to remove the file itself also
        return new Response('{}');
    }

    /**
     * Updates information about a photo based on data in a form at the edit album page.
     *
     * @param $photo_id
     *
     * @return Response
     */
    public function editPhotoAction($photo_id)
    {
        $request = Request::createFromGlobals();
        $photo = $this->getDoctrine()
            ->getRepository('AppBundle:Photo')
            ->find($photo_id);
        $comment = $request->get('comment');
        $date = new \DateTime($request->get('date'));
        $photo->setComment($comment);
        $photo->setDateTaken($date);
        $em = $this->getDoctrine()->getEntityManager();
        $em->persist($photo);
        $em->flush();

        return new Response('  comment:'.$comment); //todo: fix
    }

    /**
     * Deletes an entire album from the database.
     *
     * @param $album_id
     *
     * @return Response
     */
    public function deleteAlbumAction($album_id)
    {
        if ($this->get('security.context')->isGranted('ROLE_SUPER_ADMIN') or
            ($this->get('security.context')->isGranted('ROLE_ADMIN') and $this->currentUserCreatedAlbum($album_id))
        ) {
            $album = $this->getDoctrine()
                        ->getRepository('AppBundle:Gallery')
                        ->find($album_id);
            $em = $this->getDoctrine()->getEntityManager();
            $em->remove($album);
            $em->flush();
        } else {
            return new Response('You are not authorized to delete this album.');
        }

        return new Response('Deleted album with id '.$album_id);
    }

    //A small helper
    private function currentUserCreatedAlbum($albumId)
    {
        $user = $this->get('security.context')->getToken()->getUser();
        //Check if this user created the album that he tries to delete
        $repository = $this->getDoctrine()->getRepository('AppBundle:Gallery');
        $album = $repository->find($albumId);

        return $album->getCreatedByUser() == $user->getId();
    }

    /**
     * Return, in JSON format, the following data on albums in database: id, name and a boolean telling if logged in
     * user can edit this gallery. Only albums that the current user is allowed to view is returned.
     *
     * @return Response
     */
    public function galleriesListGetAction()
    { //todo: change name to albumsListGetAction
        //Get the albums from database
        $repository = $this->getDoctrine()->getRepository('AppBundle:Gallery');
        $albums = $repository->findViewableForCurrentUser($this->get('security.context'));
        $jsonAsArray = array();
        foreach ($albums as $album) {
            $albumFields = array();
            $albumFields['id'] = $album->getId();
            $albumFields['name'] = $album->getName();
            //Check if current user are allowed to edit this gallery
            $securityContext = $this->get('security.context');
            $canEditGallery = false;
            if ($securityContext->isGranted('IS_AUTHENTICATED_FULLY')) {
                if ($securityContext->isGranted('ROLE_SUPER_ADMIN')) {
                    $canEditGallery = true;
                } elseif ($securityContext->isGranted('ROLE_ADMIN')) {
                    if ($album->getCreatedByUser()->getId() == $securityContext->getToken()->getUser()->getId()) {
                        $canEditGallery = true;
                    }
                }
            }
            $albumFields['canEdit'] = $canEditGallery;
            $jsonAsArray[] = $albumFields;
        }
        $response = new Response(json_encode($jsonAsArray));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * Returns a div with an img-tag for each photo in the album requested.
     *
     * @param $album_id
     *
     * @return Response
     *
     * @throws \Exception
     */
    public function photosGetAction($album_id)
    {
        //todo: Check that user are allowed to view these photos. It is handled elsewhere now, but should be taken care of here.
        //Check if an album with this id exists, if not throw an exception. This shouldn't happen
        //and indicates an error in front end code
        $album = $this->getDoctrine()->getRepository('AppBundle:Gallery')->find($album_id);
        if ($album == null) {
            throw new \Exception('Attempted to load photos from non existing album. $album_id: '.$album_id);
        }
        $photos = $this->getDoctrine()->getRepository('AppBundle:Photo')->findBy(
            array('gallery' => $album_id),
            array('dateAdded' => 'ASC')
        );

        return $this->render('gallery/gallery_photos.html.twig', array('photos' => $photos));
    }

    /**
     * Render and returns the page where user can edit an album.
     *
     * @param $album_id
     *
     * @return Response
     */
    public function editAlbumAction($album_id)
    { //todo: change name
        $photos = $this->getDoctrine()->getRepository('AppBundle:Photo')->findBy(
            array('gallery' => $album_id),
            array('dateAdded' => 'ASC')
        );
        $photosArray = array();
        foreach ($photos as $photo) {
            $photoFields = array();
            $photoFields['id'] = $photo->getId();
            $photoFields['user_id'] = $photo->getAddedByUser()->getId();
            $photoFields['user_name'] = $photo->getAddedByUser()->getFirstName().' '.$photo->getAddedByUser()->getLastName();
            $photoFields['gallery_id'] = $photo->getGallery()->getId();
            $photoFields['gallery_name'] = $photo->getGallery()->getName();
            $photoFields['date_added'] = $photo->getDateAdded();
            $photoFields['date_taken'] = $photo->getDateTaken();
            $photoFields['comment'] = $photo->getComment();
            $photoFields['path'] = $photo->getPathToFile();
            $photosArray[] = $photoFields;
        }

        return $this->render('gallery/album_edit_form.html.twig', array('album_id' => $album_id, 'photos' => $photosArray));
    }

    /**
     * Uploads all the photos when OK i pressed on the upload photo form.
     *
     * @return Response
     *
     * @throws \Exception
     */
    public function uploadAction()
    {
        $request = Request::createFromGlobals();
        //Get the target folder for the photo from paramters.yml
        $output_dir = $this->container->getParameter('gallery_folder').'/';
        //Create a FileUploader
        $uploader = new FileUploader($output_dir, $this->allowed_file_types);
        //Upload the files aand get the new names created for each file
        $mappedFileNames = $uploader->upload($request);

        //Now all the files are copied to their destination folder
        //The array $mappedFileNames contains entries: $orginalFileName => fileNameWithPathOnSystemNow

        $responseText = '<html> Lastet opp følgende filer: <br>';
        $em = $this->getDoctrine()->getManager();
        foreach ($mappedFileNames as $key => $val) {
            $responseText .= $key.' to '.$val.'<br>';
            //Update the database
            //The text sent with the picture is a parameter with the same name as image + '_text'
            //http://stackoverflow.com/questions/68651/get-php-to-stop-replacing-characters-in-get-or-post-arrays
            //todo: the above SO thing is annoying.
            $commentParameterName = str_replace('.', '_', $key);
            $commentParameterName = str_replace(' ', '_', $commentParameterName);

            $text = $request->get($commentParameterName.'_text');
            $takenDate = $request->get($commentParameterName.'_taken_date_text');
            //Get taken date

            //Get the gallery id
            $gallery_id = $request->get('gallery_id');
            //Create an instance of the entity
            $photo = new Photo();
            $photo->setComment($text);
            $gallery = $this->getDoctrine()->getRepository('AppBundle:Gallery')->find($gallery_id);
            $photo->setGallery($gallery);
            $photo->setDateAdded(new \DateTime('now'));
            $photo->setDateTaken(new \DateTime($takenDate));
            $photo->setAddedByUser($this->get('security.context')->getToken()->getUser());
            $photo->setPathToFile($val);
            //Manage
            $em->persist($photo);
        }
        //Persist
        $em->flush();
        $responseText = $responseText.'</html>';

        return new Response($responseText);
    }

    /**
     * Adds an album to the database.
     * Returns a JSON with the updated list of galleries viewable by user.
     *
     * @return Response
     */
    public function createGalleryAction()
    { //todo: change name to createAlbumAction
        $request = Request::createFromGlobals();
        $galleryName = $request->request->get('gallery_name', 'Ikke navngitt galleri.');
        $private = $request->request->get('private_gallery', 'public');
        $gallery = new Gallery();
        $gallery->setName($galleryName);
        $gallery->setPrivate($private != 'public');
        $gallery->setDateCreated(new \DateTime('now'));
        $gallery->setCreatedByUser($this->get('security.context')->getToken()->getUser());
        $em = $this->getDoctrine()->getManager();
        $em->persist($gallery);
        $em->flush();
        //return the updated list of galleries
        $response = $this->forward('AppBundle:Gallery:galleriesListGet');

        return $response;
    }
}
