<?php

namespace AppBundle\Controller;

use AppBundle\Event\TeamInterestCreatedEvent;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use AppBundle\Entity\Department;
use AppBundle\Entity\TeamInterest;
use AppBundle\Form\Type\TeamInterestType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class TeamInterestController extends BaseController
{

    /**
     * @Route(name="team_interest_form", path="/teaminteresse/{id}",
     *      requirements={"id"="\d+"})
     * @Method({"GET","POST"})
     *
     * @param \AppBundle\Entity\Department|NULL $department
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function showTeamInterestFormAction(Department $department, Request $request)
    {
        $semester = $this->getDoctrine()->getRepository('AppBundle:Semester')->findCurrentSemester();
        if ($semester === null) {
            throw new BadRequestHttpException('No current semester');
        }

        $teamInterest = new TeamInterest();
        $teamInterest->setSemester($semester);
        $teamInterest->setDepartment($department);
        $form = $this->createForm(new TeamInterestType(), $teamInterest, array(
            'department' => $department,
        ));
        $form->handleRequest($request);

        if ($form->isValid()) {
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($teamInterest);
            $manager->flush();

            $this->get('event_dispatcher')->dispatch(TeamInterestCreatedEvent::NAME, new TeamInterestCreatedEvent($teamInterest));

            return $this->redirectToRoute('team_interest_form', array(
                'id' => $department->getId(),
            ));
        }

        return $this->render(':team_interest:team_interest.html.twig', array(
            'form' => $form->createView(),
        ));
    }
}
