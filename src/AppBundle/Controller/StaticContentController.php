<?php

namespace AppBundle\Controller;

use AppBundle\Entity\StaticContent;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class StaticContentController extends BaseController
{
    /**
     * Updates the static text content in database.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function updateAction(Request $request)
    {
        if (!$this->get('app.role_extension')->userCanEditPage()) {
            throw $this->createAccessDeniedException();
        }

        $htmlId = $request->get('editorID');
        $newContent = $request->get('editabledata', '');
        if (!$htmlId) {
            throw new BadRequestHttpException("Invalid htmlID $htmlId");
        }

        $em = $this->getDoctrine()->getManager();
        $content = $em->getRepository('AppBundle:StaticContent')->findOneByHtmlId($htmlId);
        if (!$content) {
            $content = new StaticContent();
            $content->setHtmlId($htmlId);
        }

        $content->setHtml($newContent);
        $em->persist($content);
        $em->flush();

        return new JsonResponse(array('status' => 'Database updated static element '.$htmlId.' New content: '.$newContent));
    }
}
