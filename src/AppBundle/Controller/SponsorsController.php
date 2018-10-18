<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\Type\SponsorType;
use AppBundle\Entity\Sponsor;

class SponsorsController extends BaseController
{
    /**
     * @Route("/kontrollpanel/sponsorer", name="sponsors_show")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function sponsorsShowAction()
    {
        $sponsors = $this->getDoctrine()
            ->getRepository('AppBundle:Sponsor')
            ->findAll();

        return $this->render('sponsors/sponsors_show.html.twig', array(
            'sponsors' => $sponsors,
        ));
    }

    /**
     * @Route("/kontrollpanel/sponsor/create", name="sponsor_create")
     * @Route("/kontrollpanel/sponsor/edit/{id}", name="sponsor_edit")
     * @param Sponsor $sponsor
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function sponsorEditAction(Sponsor $sponsor = null, Request $request)
    {
        $isCreate = $sponsor === null;
        $oldImgPath = "";
        if ($isCreate) {
            $sponsor = new Sponsor();
        } else {
            $oldImgPath = $sponsor->getLogoImagePath();
        }

        $form = $this->createForm(SponsorType::class, $sponsor);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if (!is_null($request->files->get('sponsor')['logoImagePath'])) {
                $imgPath = $this->get('app.file_uploader')->uploadSponsor($request);
                $this->get('app.file_uploader')->deleteSponsor($oldImgPath);

                $sponsor->setLogoImagePath($imgPath);
            } else {
                $sponsor->setLogoImagePath($oldImgPath);
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($sponsor);
            $em->flush();

            $this->addFlash(
                "success",
                "Sponsor {$sponsor->getName()} ble " . ($isCreate ? "opprettet" : "endret")
            );

            return $this->redirectToRoute("sponsors_show");
        }

        return $this->render("sponsors/sponsor_edit.html.twig", [
            "form" => $form->createView(),
            "sponsor" => $sponsor,
            "is_create" => $isCreate
        ]);
    }

    /**
     * @Route("/kontrollpanel/sponsor/delete/{id}", name="sponsor_delete")
     * @param Sponsor $sponsor
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteSponsorAction(Sponsor $sponsor)
    {
        if ($sponsor->getLogoImagePath()) {
            $this->get('app.file_uploader')->deleteSponsor($sponsor->getLogoImagePath());
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($sponsor);
        $em->flush();

        $this->addFlash("success", "Sponsor {$sponsor->getName()} ble slettet.");
        return $this->redirectToRoute("sponsors_show");
    }
}
