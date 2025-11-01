<?php

namespace AppBundle\Controller;

use AppBundle\Entity\StaticContent;
use AppBundle\Twig\Extension\RoleExtension;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class StaticContentController extends BaseController
{
    private $roleExtension;
    private $entityManager;

    /**
     * @param RoleExtension $roleExtension
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        RoleExtension $roleExtension,
        EntityManagerInterface $entityManager
    ) {
        $this->roleExtension = $roleExtension;
        $this->entityManager = $entityManager;
    }
    /**
     * Updates the static text content in database.
     *
     * @param Request $request
     * @return JsonResponse
     */
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

        $content = $this->entityManager->getRepository(StaticContent::class)->findOneByHtmlId($htmlId);
        if (!$content) {
            $content = new StaticContent();
            $content->setHtmlId($htmlId);
        }

        $content->setHtml($newContent);
        $this->entityManager->persist($content);
        $this->entityManager->flush();

        return new JsonResponse(array('status' => 'Database updated static element '.$htmlId.' New content: '.$newContent));
    }
}
