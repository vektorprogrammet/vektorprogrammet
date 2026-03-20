<?php

namespace App\Controller;

use App\Entity\Repository\DepartmentRepository;
use App\Shared\Repository\SemesterRepository;
use App\Entity\Sponsor;
use App\Form\Type\SponsorType;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SponsorsController extends BaseController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly FileUploader $fileUploader,
        DepartmentRepository $departmentRepo,
        SemesterRepository $semesterRepo,
    ) {
        parent::__construct($departmentRepo, $semesterRepo);
    }

    /**
     * @return Response
     */
    #[Route('/kontrollpanel/sponsorer', name: 'sponsors_show')]
    public function sponsorsShowAction()
    {
        $sponsors = $this->em->getRepository(Sponsor::class)->findAll();

        return $this->render('sponsors/sponsors_show.html.twig', [
            'sponsors' => $sponsors,
        ]);
    }

    /**
     * @return RedirectResponse|Response
     */
    #[Route('/kontrollpanel/sponsor/create', name: 'sponsor_create')]
    #[Route('/kontrollpanel/sponsor/edit/{id}', name: 'sponsor_edit')]
    public function sponsorEditAction(Request $request, ?Sponsor $sponsor = null)
    {
        $isCreate = $sponsor === null;
        $oldImgPath = '';
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

            $this->em->persist($sponsor);
            $this->em->flush();

            $this->addFlash(
                'success',
                "Sponsor {$sponsor->getName()} ble ".($isCreate ? 'opprettet' : 'endret')
            );

            return $this->redirectToRoute('sponsors_show');
        }

        return $this->render('sponsors/sponsor_edit.html.twig', [
            'form' => $form->createView(),
            'sponsor' => $sponsor,
            'is_create' => $isCreate,
        ]);
    }

    /**
     * @return RedirectResponse
     */
    #[Route('/kontrollpanel/sponsor/delete/{id}', name: 'sponsor_delete')]
    public function deleteSponsorAction(Sponsor $sponsor)
    {
        if ($sponsor->getLogoImagePath()) {
            $this->fileUploader->deleteSponsor($sponsor->getLogoImagePath());
        }

        $this->em->remove($sponsor);
        $this->em->flush();

        $this->addFlash('success', "Sponsor {$sponsor->getName()} ble slettet.");

        return $this->redirectToRoute('sponsors_show');
    }
}
