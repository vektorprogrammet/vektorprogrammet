<?php

namespace AppBundle\Service;

use AppBundle\Entity\Department;
use AppBundle\Entity\Repository\ApplicationRepository;
use AppBundle\Entity\Receipt;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class ReceiptManager
{
    private $em;

    /**
     * LetterManager constructor.
     *
     * @param EntityManager     $em
     */
    public function __construct(\Swift_Mailer $mailer, \Twig_Environment $twig, EntityManager $em)
    {
        $this->em = $em;
    }

    // TODO: This name though.... What datatype should be used?
   /* public function getTotalActiveReceiptDataPerPerson(User $user){
        $receipts = $this->em->getRepository('AppBundle:Receipt')->findActiveByDepartment($user->getDepartment());

        $receipts_data = array();
        foreach ($receipts as $receipt)
    }*/

    public function getSumByUser(User $user): float
    {
        $sum = 0.0;
        $user_receipts = $this->em->getRepository('AppBundle:Receipt')->findActiveByUser($user);
        foreach ($user_receipts as $receipt)
            $sum += $receipt->sum;

        return $sum;
    }
}
