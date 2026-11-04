<?php

namespace AppBundle\Controller;

use AppBundle\Role\Roles;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\InterviewSchema;
use AppBundle\Form\Type\InterviewSchemaType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

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
     * @return RedirectResponse|Response
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
     * @return RedirectResponse|Response
     */
    public function editSchemaAction(Request $request, InterviewSchema $schema)
    {
        $form = $this->createForm(InterviewSchemaType::class, $schema);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $questionForms = $form->get('interviewQuestions');
            $keys = array_map('strval', array_keys($questionForms->all()));
            $submittedOrder = $form->get('questionOrder')->getData();
            $order = $submittedOrder ? json_decode($submittedOrder, true) : $keys;
            if (!is_array($order) || array_values($order) !== $order) {
                throw new BadRequestHttpException('Invalid question order.');
            }
            foreach ($order as $key) {
                if (!is_string($key) || !in_array($key, $keys, true)) {
                    throw new BadRequestHttpException('Invalid question order.');
                }
            }
            if (count($order) !== count($keys) || count(array_unique($order)) !== count($keys)) {
                throw new BadRequestHttpException('Invalid question order.');
            }
            $questions = array_map(static function ($key) use ($questionForms) {
                return $questionForms->get($key)->getData();
            }, $order);

            $em = $this->getDoctrine()->getManager();
            $em->getConnection()->beginTransaction();
            try {
                $em->persist($schema);
                $em->flush(); // Assign IDs to newly added questions before recording their order.
                $schema->setQuestionOrder($questions);
                $em->flush();
                $em->getConnection()->commit();
            } catch (\Throwable $exception) {
                $em->getConnection()->rollBack();
                throw $exception;
            }
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
     * @return Response
     */
    public function showSchemasAction()
    {
        $schemas = $this->getDoctrine()->getRepository(InterviewSchema::class)->findAll();

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
