<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Sponsor;
use AppBundle\Form\Type\SponsorType;
use AppBundle\Service\Contract\FileUploaderInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SponsorsController extends BaseController
{
    private $fileUploader;
    private $entityManager;

    /**
     * @param FileUploaderInterface $fileUploader
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        FileUploaderInterface $fileUploader,
        EntityManagerInterface $entityManager
    ) {
        $this->fileUploader = $fileUploader;
        $this->entityManager = $entityManager;
    }
    /**
     * @Route("/kontrollpanel/sponsorer", name="sponsors_show")
     *
     * @return Response
     */
    public function sponsorsShowAction()
    {
        $sponsors = $this->entityManager
            ->getRepository(Sponsor::class)
            ->findAll();

        return $this->render('sponsors/sponsors_show.html.twig', array(
            'sponsors' => $sponsors,
        ));
    }

    /**
     * @Route("/kontrollpanel/sponsor/create", name="sponsor_create")
     * @Route("/kontrollpanel/sponsor/edit/{id}", name="sponsor_edit")
     * @param Sponsor|null $sponsor
     * @param Request $request
     *
     * @return RedirectResponse|Response
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
                $imgPath = $this->fileUploader->uploadSponsor($request);
                $this->fileUploader->deleteSponsor($oldImgPath);

                $sponsor->setLogoImagePath($imgPath);
            } else {
                $sponsor->setLogoImagePath($oldImgPath);
            }

            $this->entityManager->persist($sponsor);
            $this->entityManager->flush();

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
     * @return RedirectResponse
     */
    public function deleteSponsorAction(Sponsor $sponsor)
    {
        if ($sponsor->getLogoImagePath()) {
            $this->fileUploader->deleteSponsor($sponsor->getLogoImagePath());
        }

        $this->entityManager->remove($sponsor);
        $this->entityManager->flush();

        $this->addFlash("success", "Sponsor {$sponsor->getName()} ble slettet.");
        return $this->redirectToRoute("sponsors_show");
    }
}
