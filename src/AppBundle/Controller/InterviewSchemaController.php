<?php

namespace AppBundle\Controller;

use AppBundle\Role\Roles;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\InterviewSchema;
use AppBundle\Form\Type\InterviewSchemaType;

/**
 * InterviewController is the controller responsible for interview actions,
 * such as showing, assigning and conducting interviews.
 */
class InterviewSchemaController extends BaseController
{
    /**
     * Shows and handles the submission of the create interview schema form.
     * Uses the same form as the edit action.
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function createSchemaAction(Request $request)
    {
        $schema = new InterviewSchema();

        return $this->editSchemaAction($request, $schema);
    }

    /**
     * Shows and handles the submission of the edit interview schema form.
     * Uses the same form as the create action.
     *
     * @param Request         $request
     * @param InterviewSchema $schema
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editSchemaAction(Request $request, InterviewSchema $schema)
    {
        $form = $this->createForm(InterviewSchemaType::class, $schema);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($schema);
            $em->flush();
            return $this->redirect($this->generateUrl('interview_schema'));
        }

        return $this->render('interview/schema.html.twig', array(
            'form' => $form->createView(),
            'schema' => $schema,
            'isCreate' => !$schema->getId()
        ));
    }

    /**
     * Shows the interview schemas page.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showSchemasAction()
    {
        $schemas = $this->getDoctrine()->getRepository('AppBundle:InterviewSchema')->findAll();

        return $this->render('interview/schemas.html.twig', array('schemas' => $schemas));
    }

    /**
     * Deletes the given interview schema.
     * This method is intended to be called by an Ajax request.
     *
     * @param InterviewSchema $schema
     *
     * @return JsonResponse
     */
    public function deleteSchemaAction(InterviewSchema $schema)
    {
        try {
            if ($this->isGranted(Roles::TEAM_LEADER)) {
                $em = $this->getDoctrine()->getManager();
                $em->remove($schema);
                $em->flush();

                $response['success'] = true;
            } else {
                $response['success'] = false;
                $response['cause'] = 'Ikke tilstrekkelig rettigheter';
            }
        } catch (Exception $e) {
            $response = ['success' => false,
                'code' => $e->getCode(),
                'cause' => 'Det oppstod en feil.',
            ];
        }

        return new JsonResponse($response);
    }
}
