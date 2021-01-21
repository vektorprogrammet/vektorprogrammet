<?php

namespace AppBundle\Controller;

use AppBundle\Entity\ParentCourse;
use AppBundle\Form\Type\ParentAssignmentType;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\ParentAssignment;
use AppBundle\Service\EmailSender;

class ParentAssignmentController extends BaseController
{
    public function showAction(ParentCourse $course)
    {
        return $this->render('parent_course/parent_course_external_show.html.twig', array(
            'parentCourse' => $course,
        ));
    }

    public function createAction(Request $request, ParentCourse $course)
    {
        $parentAssigned = new ParentAssignment();
        $form = $this->createForm(ParentAssignmentType::class, $parentAssigned);
        $form->handleRequest($request);


        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $randomString = uniqid();
            $parentAssignedWithGeneratedKey = $em->getRepository('AppBundle:ParentAssignment')->getParentAssignmentByUniqueKey($randomString);
            while ($parentAssignedWithGeneratedKey != null) {
                $randomString = uniqid();
                $parentAssignedWithGeneratedKey = $em->getRepository('AppBundle:ParentAssignment')->getParentAssignmentByUniqueKey($randomString);
            }

            $parentAssigned->setUniqueKey($randomString);
            $parentAssigned->setCourse($course);
            $em->persist($parentAssigned);
            $em->flush();

            $this->addFlash("success", "Din påmelding er registert.");

            $this->get(EmailSender::class)->sendParentCourseAssignmentConfirmation($parentAssigned);

            return $this->redirect($this->generateUrl('parents'));
        }

        return $this->render('parents/parent-assignment-create.html.twig', array(
            'form' => $form->createView(),
            'parentCourse' => $course,
            ));
    }

    public function externalDeleteAction(int $uniqueKey)
    {
        $em = $this->getDoctrine()->getManager();

        $parent = $em->getRepository('AppBundle:ParentAssignment')->getParentAssignmentByUniqueKey($uniqueKey);
        #dump($parent);
        $em->remove($parent);
        $em->flush();
        #denne skal brukes til å slette fra mailen som sendes ut til foreldrene!

        /* Use as inspiration!
        $subscriber = $this->getDoctrine()->getRepository('AppBundle:AdmissionSubscriber')->findByUnsubscribeCode($code);
        $this->addFlash('title', 'Opptaksvarsel - Avmelding');
        if (!$subscriber) {
            $this->addFlash('message', "Du vil ikke lengre motta varsler om opptak");
        } else {
            $email = $subscriber->getEmail();
            $this->addFlash('message', "Du vil ikke lengre motta varsler om opptak på $email");
            $em = $this->getDoctrine()->getManager();
            $em->remove($subscriber);
            $em->flush();
        }


        return $this->redirectToRoute('confirmation');

        */
        $this->addFlash('message', "Du er ikke lenger påmeldt :( Meld deg gjerne på igjen dersom du kjenner at du angrer!");

        return $this->redirectToRoute('parents');
    }

    public function sendEmailAction(ParentAssignment $parentAssignment)
    {
        $this->get(EmailSender::class)->sendParentCourseAssignmentConfirmation($parentAssignment);
        $this->addFlash("success", "Sendt");
        $response['success'] = true;

        return new JsonResponse($response);
    }
};
