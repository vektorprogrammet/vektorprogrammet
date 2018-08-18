<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use AppBundle\Entity\Department;
use AppBundle\Entity\TeamInterest;
use AppBundle\Form\Type\TeamInterestType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class TeamInterestControllerController extends Controller
{

    /**
     * @Route(name="team_interest_form", path="/teaminteresse/{id}",
     *     defaults={"id"=null}, requirements={"id"="\d+"})
     * @Method({"GET","POST"})
     *
     * @param \AppBundle\Entity\Department|NULL $department
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
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
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($teamInterest);
            $manager->flush();

            return $this->redirectToRoute('team_interest_form', array(
                'id' => $department->getId(),
            ));
        }

        return $this->render(':team_interest:team_interest.html.twig', array(
            'form' => $form->createView(),
        ));
    }
}
