<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Application;
use AppBundle\Entity\Team;
use AppBundle\Entity\TeamApplication;
use AppBundle\Form\Type\TeamApplicationType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class TeamApplicationController extends Controller
{
    public function showApplicationAction(TeamApplication $application){
        return $this->render('team/show_application.html.twig',array(
            'application'=>$application
        ));
    }
    public function showAllApplicationsAction(Team $team){
        $applications = $this->getDoctrine()->getRepository('AppBundle:TeamApplication')->findByTeam($team);
        return $this->render('team/show_applications.html.twig',array(
            'applications'=>$applications,
            'team'=>$team
        ));
    }

    public function showAction(Team $team, Request $request)
    {
        $teamApplication = new TeamApplication();
        $form = $this->createForm(new TeamApplicationType(), $teamApplication);
        $form->handleRequest($request);
        if ($form->isValid() && $form->isSubmitted()){
            $teamApplication->setTeam($team);
            $manager=$this->getDoctrine()->getManager();
            $manager->persist($teamApplication);
            $manager->flush();
            $emailMessage=\Swift_Message::newInstance()
                ->setSubject('Ny søker til '.$team->getName())
                ->setFrom(array('vektorprogrammet@vektorprogrammet.no'=>'Vektorprogrammet'))
                ->setReplyTo($teamApplication->getEmail())
                ->setTo($team->getEmail())
                ->setBody($this->renderView('team/application_email.html.twig',array(
                    'application' => $teamApplication
                )));
            $this->get('mailer')->send($emailMessage);

            $receipt=\Swift_Message::newInstance()
                ->setSubject('Søknad til '.$team->getName().' mottatt')
                ->setFrom(array($team->getEmail()=>$team->getName()))
                ->setReplyTo($team->getEmail())
                ->setTo($teamApplication->getEmail())
                ->setBody($this->renderView('team/receipt.html.twig',array(
                    'team' => $team
                )));
            $this->get('mailer')->send($receipt);


            return $this->render('team/confirmation.html.twig');

        }
        return $this->render('team/team_application.html.twig',array(
            'team' => $team,
            'form' => $form->createView()
    ));

    }
}
