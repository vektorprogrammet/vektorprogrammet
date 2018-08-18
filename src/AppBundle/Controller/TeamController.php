<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Department;
use AppBundle\Entity\TeamInterest;
use AppBundle\Form\Type\TeamInterestType;
use AppBundle\Role\Roles;
use AppBundle\Entity\Team;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TeamController extends Controller
{
    public function showAction(Team $team)
    {
        if (!$team->isActive() && !$this->isGranted(Roles::TEAM_MEMBER)) {
            throw new NotFoundHttpException('Team not found');
        }

        return $this->render('team/team_page.html.twig', array(
            'team'  => $team,
        ));
    }

    public function showByDepartmentAndTeamAction($departmentCity, $teamName)
    {
        $teams = $this->getDoctrine()->getRepository('AppBundle:Team')->findByCityAndName($departmentCity, $teamName);
        if (count($teams) !== 1) {
            throw new NotFoundHttpException('Team not found');
        }
        return $this->showAction($teams[0]);
    }

    public function indexAction()
    {
        return $this->render('team/index.html.twig');
    }

    public function showTeamInterestFormAction(Department $department = null, Request $request)
    {
        $departmentInUrl = $department !== null;
        if (!$departmentInUrl) {
            $user = $this->getUser();
            if ($user !== null) {
                $department = $user->getDepartment();
            } else {
                throw new BadRequestHttpException('No department specified');
            }
        }

        $teamInterest = new TeamInterest();
        $form = $this->createForm(new TeamInterestType(), $teamInterest, array(
            'department' => $department,
        ));
        $form->handleRequest($request);

        if ($form->isValid()) {
            // TODO: Check if the email is unique, if not, add new teams to interest list

            $manager = $this->getDoctrine()->getManager();
            $manager->persist($teamInterest);
            $manager->flush();

            return $this->redirectToRoute('team_interest_form', array(
                'id' => $department->getId(),
            ));
        }

        return $this->render(':team:team_interest.html.twig', array(
            'form' => $form->createView(),
        ));
    }
}
