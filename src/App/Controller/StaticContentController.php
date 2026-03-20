<?php

namespace App\Controller;

use App\Support\Controller\BaseController;
use App\Entity\Repository\DepartmentRepository;
use App\Shared\Repository\SemesterRepository;
use App\Entity\StaticContent;
use App\Support\Twig\RoleExtension;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Attribute\Route;

class StaticContentController extends BaseController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly RoleExtension $roleExtension,
        DepartmentRepository $departmentRepo,
        SemesterRepository $semesterRepo,
    ) {
        parent::__construct($departmentRepo, $semesterRepo);
    }

    /**
     * Updates the static text content in database.
     *
     * @return JsonResponse
     */
    #[Route('/updatestaticcontent', name: 'update_static_content', methods: ['POST'])]
    public function updateAction(Request $request)
    {
        if (!$this->roleExtension->userCanEditPage()) {
            throw $this->createAccessDeniedException();
        }

        $htmlId = $request->get('editorID');
        $newContent = $request->get('editabledata', '');
        if (!$htmlId) {
            throw new BadRequestHttpException("Invalid htmlID $htmlId");
        }

        $content = $this->em->getRepository(StaticContent::class)->findOneByHtmlId($htmlId);
        if (!$content) {
            $content = new StaticContent();
            $content->setHtmlId($htmlId);
        }

        $content->setHtml($newContent);
        $this->em->persist($content);
        $this->em->flush();

        return new JsonResponse(['status' => 'Database updated static element '.$htmlId.' New content: '.$newContent]);
    }
}
