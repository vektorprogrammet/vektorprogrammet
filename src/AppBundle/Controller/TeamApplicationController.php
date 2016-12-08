<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Application;
use AppBundle\Entity\Team;
use AppBundle\Entity\TeamApplication;
use AppBundle\Form\Type\TeamApplicationType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class TeamApplicationController extends Controller
{
    public function showApplicationAction(TeamApplication $application)
    {
        $user = $this->getUser();
        $activeUserHistoriesInTeam = $this->getDoctrine()->getRepository('AppBundle:WorkHistory')->findActiveWorkHistoriesByTeamAndUser($application->getTeam(), $user);
        if (empty($activeUserHistoriesInTeam) && !$this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN')) {
            throw new AccessDeniedHttpException();
        }

        return $this->render('team_admin/show_application.html.twig', array(
            'application' => $application,
        ));
    }
    public function showAllApplicationsAction(Team $team)
    {
        $applications = $this->getDoctrine()->getRepository('AppBundle:TeamApplication')->findByTeam($team);
        $user = $this->getUser();
        $activeUserHistoriesInTeam = $this->getDoctrine()->getRepository('AppBundle:WorkHistory')->findActiveWorkHistoriesByTeamAndUser($team, $user);
        if (empty($activeUserHistoriesInTeam) && !$this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN')) {
            throw new AccessDeniedHttpException();
        }

        return $this->render('team_admin/show_applications.html.twig', array(
            'applications' => $applications,
            'team' => $team,
        ));
    }

    public function deleteTeamApplicationByIdAction(TeamApplication $teamApplication)
    {
        $manager = $this->getDoctrine()->getEntityManager();

        $manager->remove($teamApplication);
        $manager->flush();

        /*
        return $this->render('team/show_applications.html.twig',array(
            'applications'=>$applications,
            'team'=>$team
        ));
        */

        return $this->redirectToRoute('team_application_show_all', array('id' => $teamApplication->getTeam()->getId()));
    }

    public function showAction(Team $team, Request $request)
    {
        if (!$team->getAcceptApplication()) {
            throw new AccessDeniedException();
        }
        $teamApplication = new TeamApplication();
        $form = $this->createForm(new TeamApplicationType(), $teamApplication);
        $form->handleRequest($request);

        if ($form->isValid() && $form->isSubmitted() && $team->getAcceptApplication()) {
            $teamApplication->setTeam($team);

            $manager = $this->getDoctrine()->getManager();
            $manager->persist($teamApplication);
            $manager->flush();

            if ($team->getEmail() !== null) {
                $emailMessage = \Swift_Message::newInstance()
                    ->setSubject('Ny søker til '.$team->getName())
                    ->setFrom(array('vektorprogrammet@vektorprogrammet.no' => 'Vektorprogrammet'))
                    ->setReplyTo($teamApplication->getEmail())
                    ->setTo($team->getEmail())
                    ->setBody($this->renderView('team/application_email.html.twig', array(
                        'application' => $teamApplication,
                    )));
                $this->get('mailer')->send($emailMessage);

                $email = $team->getEmail();
            } else {
                $email = $team->getDepartment()->getEmail();
            }

            $receipt = \Swift_Message::newInstance()
                ->setSubject('Søknad til '.$team->getName().' mottatt')
                ->setFrom(array($email => $team->getName()))
                ->setReplyTo($email)
                ->setTo($teamApplication->getEmail())
                ->setBody($this->renderView('team/receipt.html.twig', array(
                    'team' => $team,
                )));
            $this->get('mailer')->send($receipt);

            return $this->render('team/confirmation.html.twig');
        }

        return $this->render('team/team_application.html.twig', array(
            'team' => $team,
            'form' => $form->createView(),
    ));
    }
}
