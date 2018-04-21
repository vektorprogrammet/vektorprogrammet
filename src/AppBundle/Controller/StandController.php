<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Semester;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class StandController extends Controller
{
    /**
     * @Route("/kontrollpanel/stand/{id}", name="stand_by_semester")
     * @Route("/kontrollpanel/stand", name="stand")
     *
     * @param Semester|null $semester
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Semester $semester = null)
    {
        if ($semester === null) {
            $semester = $this->getUser()->getDepartment()->getCurrentOrLatestSemester();
        }
        $subscribers = $this->getDoctrine()->getRepository('AppBundle:AdmissionSubscriber')->findByDepartment($semester->getDepartment());
        $subData = [];
        $now = new \DateTime();
        foreach ($subscribers as $subscriber) {
            $days = $subscriber->getTimestamp()->diff($now)->days;
            if ($days > 30) {
                continue;
            }

            $date = $subscriber->getTimestamp()->format('Y-m-d');
            if (!isset($subData[$date])) {
                $subData[$date] = 0;
            }
            $subData[$date]++;
        }
        ksort($subData);

        return $this->render('stand_admin/stand.html.twig', [
            'semester' => $semester,
            'subscribers' => $subscribers,
            'subData' => $subData,
        ]);
    }
}
