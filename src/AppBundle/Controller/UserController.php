<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class UserController extends Controller {

	/**
	 * @Route("/profil/partnere", name="my_partners")
	 *
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function myPartnerAction( ) {
		if (!$this->getUser()->isActive()) {
			throw $this->createAccessDeniedException();
		}
		$activeAssistantHistories = $this->getDoctrine()->getRepository('AppBundle:AssistantHistory')->findActiveAssistantHistoriesByUser($this->getUser());
		if (empty($activeAssistantHistories)) {
			throw $this->createNotFoundException();
		}

		$partnerInformations = [];

		foreach ($activeAssistantHistories as $activeHistory) {
			$schoolHistories = $this->getDoctrine()->getRepository('AppBundle:AssistantHistory')->findActiveAssistantHistoriesBySchool($activeHistory->getSchool());
			$partners = [];

			foreach ($schoolHistories as $sh) {
				if ($sh->getUser() === $this->getUser()) {
					continue;
				}
				if ($sh->getDay() !== $activeHistory->getDay()) {
					continue;
				}
				if ($activeHistory->activeInGroup(1) && $sh->activeInGroup(1) ||
				    $activeHistory->activeInGroup(2) && $sh->activeInGroup(2)){
					$partners[] = $sh;
				}
			}
			$partnerInformations[] = [
				'school' => $activeHistory->getSchool(),
				'assistantHistory' => $activeHistory,
				'partners' => $partners,
			];
		}

		$semester = $this->getUser()->getDepartment()->getCurrentSemester();
		return $this->render( 'user/my_partner.html.twig', [
			'partnerInformations' => $partnerInformations,
			'semester' => $semester
		] );
	}
}
