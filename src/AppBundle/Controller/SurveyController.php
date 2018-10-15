<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Semester;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Form\Type\SurveySchoolSpecificExecuteType;
use http\Exception\InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Survey;
use AppBundle\Form\Type\SurveyType;
use AppBundle\Form\Type\SurveyExecuteType;
use AppBundle\Role\Roles;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * SurveyController is the controller responsible for survey actions,
 * such as showing, assigning and conducting surveys.
 */
class SurveyController extends Controller
{


    /**
     * Shows the given survey.
     *
     * @param Request $request
     * @param Survey  $survey
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Request $request, Survey $survey)
    {
        if ($survey->isTeamSurvey()) {
            return $this->redirectToRoute('survey_show_team', array('id' => $survey->getId()));
        }


        $surveyTaken = $this->get('survey.manager')->initializeSurveyTaken($survey);

        $form = $this->createForm(SurveySchoolSpecificExecuteType::class, $surveyTaken, array(
            'validation_groups' => array('schoolSpecific'),
        ));
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $surveyTaken->removeNullAnswers();
            $surveyTaken->setTime(new \DateTime());

            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($surveyTaken);
                $em->flush();

                if ($survey->isShowCustomFinishPage()) {
                    return $this->render('survey/finish_page.html.twig', [
                        'content' => $survey->getFinishPageContent(),
                    ]);
                }

                $this->addFlash('success', 'Mottatt svar!');
            } else {
                $this->addFlash('warning', 'Svaret ditt ble ikke sendt! Du må fylle ut alle obligatoriske felter.');
            }

            //New form without previous answers
            return $this->redirectToRoute('survey_show', array('id' => $survey->getId()));
        }


        return $this->render('survey/takeSurvey.html.twig', array(
            'form' => $form->createView(),

        ));
    }

    public function showTeamAction(Request $request, Survey $survey)
    {
        $user = $this->getUser();

        if (!$survey->isTeamSurvey()) {
            return $this->redirectToRoute('survey_show', array('id' => $survey->getId()));
        }
        if ($user===null) {
            throw new AccessDeniedException("Dette er en teamundersøkese. Logg inn for å ta den!");
        }

        $surveyTaken = $this->get('survey.manager')->initializeSurveyTaken($survey);

        $form = $this->createForm(SurveyExecuteType::class, $surveyTaken);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $surveyTaken->removeNullAnswers();
            $surveyTaken->setTime(new \DateTime());
            $surveyTaken->setUser($user);


            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $allTakenSurveys = $em
                    ->getRepository('AppBundle:SurveyTaken')
                    ->findAllSurveyTakenBySurveyAndUser($survey, $user);


                if (!empty($allTakenSurveys)) {
                    foreach ($allTakenSurveys as $oldTakenSurvey) {
                        $em->remove($oldTakenSurvey);
                    }
                }
                $em->persist($surveyTaken);
                $em->flush();



                if ($survey->isShowCustomFinishPage()) {
                    return $this->render('survey/finish_page.html.twig', [
                        'content' => $survey->getFinishPageContent(),
                    ]);
                }
                $this->addFlash('success', 'Mottatt svar!');
            } else {
                $this->addFlash('warning', 'Svaret ditt ble ikke sendt! Du må fylle ut alle obligatoriske felter.');
            }

            //New form without previous answers
            return $this->redirectToRoute('survey_show_team', array('id' => $survey->getId()));
        }

        return $this->render('survey/takeSurvey.html.twig', array(
            'form' => $form->createView(),

        ));
    }

    public function showAdminAction(Request $request, Survey $survey)
    {
        if ($survey->isTeamSurvey()) {
            throw new \InvalidArgumentException("Er team undersøkelse og har derfor ingen admin utfylling");
        }
        $surveyTaken = $this->get('survey.manager')->initializeSurveyTaken($survey);
        $surveyTaken = $this->get('survey.manager')->predictSurveyTakenAnswers($surveyTaken);

        $form = $this->createForm(SurveySchoolSpecificExecuteType::class, $surveyTaken);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $surveyTaken->removeNullAnswers();
            $surveyTaken->setTime(new \DateTime());



            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($surveyTaken);
                $em->flush();

                $this->addFlash('undersokelse-notice', 'Mottatt svar!');
            } else {
                $this->addFlash('undersokelse-warning', 'Svaret ditt ble ikke sendt! Du må fylle ut alle obligatoriske felter.');
            }

            //New form without previous answers
            return $this->redirectToRoute('survey_show_admin', array('id' => $survey->getId()));
        }

        return $this->render('survey/takeSurvey.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    public function createSurveyAction(Request $request)
    {
        $survey = new Survey();
        $surveyType = new SurveyType();
        if ($this->isUserAdmin()) {
            $surveyType->setAdminSurvey(true);
        }
        $form = $this->createForm($surveyType, $survey);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($survey);
            $em->flush();

            // Need some form of redirect. Will cause wrong database entries if the form is rendered again
            // after a valid submit, without remaking the form with up to date question objects from the database.
            return $this->redirect($this->generateUrl('surveys'));
        }

        return $this->render('survey/survey_create.html.twig', array(
            'form' => $form->createView(),
            'survey' => $survey
        ));
    }

    public function copySurveyAction(Request $request, Survey $survey)
    {
        $tempSurveyType = new SurveyType();
        if ($this->isUserAdmin()) {
            $tempSurveyType->setAdminSurvey(true);
        } elseif ($survey->isTeamSurvey()) {
            throw $this->createAccessDeniedException();
        }


        $em = $this->getDoctrine()->getManager();
        $department = $this->getUser()->getDepartment();
        $semester = $em->getRepository('AppBundle:Semester')->findCurrentSemesterByDepartment($department);

        $surveyClone = $survey->copy();
        $surveyClone->setSemester($semester);


        $form = $this->createForm($tempSurveyType, $surveyClone);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($surveyClone);
            $em->flush();

            return $this->redirect($this->generateUrl('surveys'));
        }

        return $this->render('survey/survey_create.html.twig', array(
            'form' => $form->createView(),
            'survey' => $survey
        ));
    }

    /**
     * @Route(
     *     "/kontrollpanel/undersokelse/admin/{id}",
     *     name="surveys",
     *     methods={"GET"},
     *     defaults={"id": null}
     * )
     *
     * @param Semester|null $semester
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showSurveysAction(Semester $semester = null)
    {
        if ($semester === null) {
            $semester = $this->getUser()->getDepartment()->getCurrentOrLatestSemester();
        }
        $surveys = $this->getDoctrine()->getRepository('AppBundle:Survey')->findBy(
            ['semester' => $semester], ['id' => 'DESC']
        );
        foreach ($surveys as $survey) {
            $totalAnswered = count($this->getDoctrine()->getRepository('AppBundle:SurveyTaken')->findBy(array('survey' => $survey)));
            $survey->setTotalAnswered($totalAnswered);
        }

        return $this->render('survey/surveys.html.twig', array(
            'surveys' => $surveys,
            'semester' => $semester
        ));
    }

    public function editSurveyAction(Request $request, Survey $survey)
    {
        $tempSurveyType = new SurveyType();
        if ($this->isUserAdmin()) {
            $tempSurveyType->setAdminSurvey(true);
        } elseif ($survey->isTeamSurvey()) {
            throw $this->createAccessDeniedException();
        }

        $form = $this->createForm($tempSurveyType, $survey);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($survey);
            $em->flush();

            // Need some form of redirect. Will cause wrong database entries if the form is rendered again
            // after a valid submit, without remaking the form with up to date question objects from the database.
            return $this->redirect($this->generateUrl('surveys'));
        }

        return $this->render('survey/survey_create.html.twig', array(
            'form' => $form->createView(),
            'survey' => $survey
        ));
    }

    /**
     * Deletes the given Survey.
     * This method is intended to be called by an Ajax request.
     *
     * @param Survey $survey
     *
     * @return JsonResponse
     */
    public function deleteSurveyAction(Survey $survey)
    {
        try {
            if ($this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN')) {
                $em = $this->getDoctrine()->getManager();
                $em->remove($survey);
                $em->flush();

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

    public function resultSurveyAction(Survey $survey)
    {
        if ($survey->isTeamSurvey()) {
            return $this->render('survey/survey_result.html.twig', array(
                'textAnswers' => $survey->getTextAnswerWithTeamResults(),
                'survey' => $survey,
                'teamSurvey' => $survey->isTeamSurvey()
            ));
        }

        return $this->render('survey/survey_result.html.twig', array(
            'textAnswers' => $survey->getTextAnswerWithSchoolResults(),
            'survey' => $survey,
            'teamSurvey' =>  $survey->isTeamSurvey(),

        ));
    }

    public function getSurveyResultAction(Survey $survey)
    {
        $surveysTaken = $this->getDoctrine()->getRepository('AppBundle:SurveyTaken')->findAllTakenBySurvey($survey);
        $validSurveysTaken = array();



        $group = array();
        $title = "";
        if ($survey->isTeamSurvey()) {
            foreach ($surveysTaken as $surveyTaken) {
                foreach ($surveyTaken->getUser()->getTeamMemberships() as $teamMembership) {
                    $group[] = $teamMembership->getTeam()->getName();
                }

                $validSurveysTaken[] = $surveyTaken;
            }


            $title = "Team";
        } else {
            foreach ($surveysTaken as $surveyTaken) {
                if (is_null($surveyTaken->getSchool())) {
                    continue;
                }

                $validSurveysTaken[] = $surveyTaken;

                if (!in_array($surveyTaken->getSchool()->getName(), $group)) {
                    $group[] = $surveyTaken->getSchool()->getName();
                }
            }

            $title = "Skole";
        }

        //Inject the school/team question into question array
        $groupQuestion = array('question_id' => 0, 'question_label' => $title, 'alternatives' => $group);
        $survey_json = json_encode($survey);
        $survey_decode = json_decode($survey_json, true);
        $survey_decode['questions'][] = $groupQuestion;


        return new JsonResponse(array('survey' => $survey_decode, 'answers' => $validSurveysTaken));
    }

    private function isUserAdmin() : bool
    {
        return $this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN');
    }


    public function toggleReservePopUpAction()
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $user->setReservedPopUp(!$user->getReservedPopUp());
        $user->setLastPopUp(new \DateTime());
        $em->persist($user);
        $em->flush();

        $response = $this->forward('AppBundle:Home:show'
        );

        return $response;
    }

    public function closePopUpAction()
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $user->setLastPopUp(new \DateTime());
        $em->persist($user);
        $em->flush();
        $response = $this->forward('AppBundle:Home:show'
        );
        return $response;
    }
}
