<?php

namespace AppBundle\Controller;

use AppBundle\Entity\ABTest;
use AppBundle\Form\Type\ABTestType;
use AppBundle\Service\ABTestManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;


class ABTestController extends BaseController
{
    public function createABTestAction(Request $request, ABTest $ABTest = null){
        if ($isCreate = $ABTest === null) {
            $ABTest = new ABTest();
        }

        $em = $this->getDoctrine()->getManager();

        $bolkNames = $em
            ->getRepository('AppBundle:AssistantHistory')
            ->findAllBolkNames();


        $form = $this->createForm(ABTestType::class, $ABTest, array(
            'bolkNames' => $bolkNames,
            'isCreate' => $isCreate,

        ));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try{
                $ABTest = $this->get(ABTestManager::class)->initializeABTest($ABTest);
            }catch (\InvalidArgumentException $e){
                $this->addFlash("danger", $e->getMessage());
                return $this->redirect($this->generateUrl('ab_group_create'));
            }catch (\UnexpectedValueException $e){
                $this->addFlash("danger", $e->getMessage());
                return $this->redirect($this->generateUrl('ab_group_create'));
            }
            $em = $this->getDoctrine()->getManager();
            $em->persist($ABTest);
            $em->flush();

            $this->addFlash("success", "Brukergruppering laget");


            // Need some form of redirect. Will cause wrong database entries if the form is rendered again
            // after a valid submit, without remaking the form with up to date question objects from the database.
            return $this->redirect($this->generateUrl('ab_group_create'));
        }

        return $this->render('ab_test/create.html.twig', array(
            'form' => $form->createView(),
            'isCreate' => $isCreate,
            'ABTest' => $ABTest,

        ));
    }
}
