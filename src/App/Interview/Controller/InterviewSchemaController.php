<?php

namespace App\Interview\Controller;

use App\Support\Controller\BaseController;
use App\Interview\Infrastructure\Entity\InterviewSchema;
use App\Organization\Infrastructure\Repository\DepartmentRepository;
use App\Shared\Repository\SemesterRepository;
use App\Interview\Form\InterviewSchemaType;
use App\Role\Roles;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * InterviewController is the controller responsible for interview actions,
 * such as showing, assigning and conducting interviews.
 */
class InterviewSchemaController extends BaseController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        DepartmentRepository $departmentRepo,
        SemesterRepository $semesterRepo,
    ) {
        parent::__construct($departmentRepo, $semesterRepo);
    }

    /**
     * Shows and handles the submission of the create interview schema form.
     * Uses the same form as the edit action.
     *
     * @return RedirectResponse|Response
     */
    #[Route('/kontrollpanel/intervju/skjema/opprett', name: 'interview_schema_create', methods: ['GET', 'POST'])]
    public function createSchemaAction(Request $request)
    {
        $schema = new InterviewSchema();

        return $this->editSchemaAction($request, $schema);
    }

    /**
     * Shows and handles the submission of the edit interview schema form.
     * Uses the same form as the create action.
     *
     * @return RedirectResponse|Response
     */
    #[Route('/kontrollpanel/intervju/skjema/{id}', name: 'interview_schema_edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function editSchemaAction(Request $request, InterviewSchema $schema)
    {
        $form = $this->createForm(InterviewSchemaType::class, $schema);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($schema);
            $this->em->flush();

            return $this->redirect($this->generateUrl('interview_schema'));
        }

        return $this->render('interview/schema.html.twig', [
            'form' => $form->createView(),
            'schema' => $schema,
            'isCreate' => !$schema->getId(),
        ]);
    }

    /**
     * Shows the interview schemas page.
     *
     * @return Response
     */
    #[Route('/kontrollpanel/intervju/skjema', name: 'interview_schema', methods: ['GET'])]
    public function showSchemasAction()
    {
        $schemas = $this->em->getRepository(InterviewSchema::class)->findAll();

        return $this->render('interview/schemas.html.twig', ['schemas' => $schemas]);
    }

    /**
     * Deletes the given interview schema.
     * This method is intended to be called by an Ajax request.
     *
     * @return JsonResponse
     */
    #[Route('/kontrollpanel/intervju/skjema/slett/{id}', name: 'interview_schema_delete', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function deleteSchemaAction(InterviewSchema $schema)
    {
        try {
            if ($this->isGranted(Roles::TEAM_LEADER)) {
                $this->em->remove($schema);
                $this->em->flush();

                $response['success'] = true;
            } else {
                $response['success'] = false;
                $response['cause'] = 'Ikke tilstrekkelig rettigheter';
            }
        } catch (\Exception $e) {
            $response = ['success' => false,
                'code' => $e->getCode(),
                'cause' => 'Det oppstod en feil.',
            ];
        }

        return new JsonResponse($response);
    }
}
