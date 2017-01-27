<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\SubstituteRepository")
 * @ORM\Table(name="substitute")
 */
class Substitute
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Interview", inversedBy="interviewAnswers")
     * @ORM\JoinColumn(name="interview_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $interview;


}
