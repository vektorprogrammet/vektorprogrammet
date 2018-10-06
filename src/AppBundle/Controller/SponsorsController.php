<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\Type\SponsorType;
use AppBundle\Entity\Sponsor;

class SponsorsController extends BaseController
{
    public function sponsorsEditAction(Request $request)
    {
        //Get all the sponsors from the database
        $sponsors = $this->getDoctrine()
            ->getRepository('AppBundle:Sponsor')
            ->findAll();

        //Create an array of forms that the twig will iterate over when rendering the page
        $forms = array();
        $logos = array();
        foreach ($sponsors as $sponsor) {
            $form = $this->createForm(new SponsorType($sponsor->getId(), $this->container->get('router')), $sponsor);
            $form = $form->createView();
            $forms[] = $form;
            $logos[] = $sponsor->getLogoImagePath();
        }

        return $this->render('sponsors/sponsors_edit.html.twig', array(
            'forms' => $forms,
            'logos' => $logos,
        ));
    }

    public function sponsorsUpdateAction(Request $request, $id)
    {
        //Get the Sponsor object from the database
        $sponsor = $this->getDoctrine()->getRepository('AppBundle:Sponsor')
            ->find($id);
        //Get the entity manager
        $em = $this->getDoctrine()->getEntityManager();
        if (array_key_exists('delete', $request->request->get('sponsor'))) { //Delete the sponsor object from the database
            $em->remove($sponsor);
        } else { //Update the sponsor object in the database
            //Empty file-field in the form is allowed
            if ($request->files->get('sponsor')['logoImagePath'] !== null) {
                $path = $this->get('app.file_uploader')->uploadLogo($request);
                $sponsor->setLogoImagePath($path);
            }
            //Get the sponsor name from the request (value entered in the form)
            $name = $request->request->get('sponsor')['name'];
            $sponsor->setName($name);
            //Get the sponsor's url
            $url = $request->request->get('sponsor')['url'];
            $sponsor->setUrl($url);
            //Get the sponsor size
            $size = $request->request->get('sponsor')['size'];
            $sponsor->setSize($size);

            //Save to database
            $em->persist($sponsor);
        }
        $em->flush();

        return $this->redirectToRoute('sponsors_edit');
    }

    public function sponsorsAddAction()
    {
        $sponsor = new Sponsor();
        $em = $this->getDoctrine()->getEntityManager();
        $em->persist($sponsor);
        $em->flush();

        return $this->redirectToRoute('sponsors_edit');
    }
}
