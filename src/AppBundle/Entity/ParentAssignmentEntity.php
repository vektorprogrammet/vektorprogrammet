<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ParentAssignmentEntity
 *
 * @ORM\Table(name="parent_assignment_entity")
 * @ORM\Entity(repositoryClass="ParentAssignmentRepository")
 */
class ParentAssignmentEntity
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="Navn", type="string", length=255, nullable=False)
     */
    private $navn;

    /**
     * @var string
     *
     * @ORM\Column(name="Epost", type="string", length=255, nullable=False)
     */
    private $epost;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="Tidspunkt", type="datetime")
     */
    private $tidspunkt;


    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set navn.
     *
     * @param string $navn
     *
     * @return ParentAssignmentEntity
     */
    public function setNavn($navn)
    {
        $this->navn = $navn;

        return $this;
    }

    /**
     * Get navn.
     *
     * @return string
     */
    public function getNavn()
    {
        return $this->navn;
    }

    /**
     * Set epost.
     *
     * @param string $epost
     *
     * @return ParentAssignmentEntity
     */
    public function setEpost($epost)
    {
        $this->epost = $epost;

        return $this;
    }

    /**
     * Get epost.
     *
     * @return string
     */
    public function getEpost()
    {
        return $this->epost;
    }

    /**
     * Set tidspunkt.
     *
     * @param \DateTime $tidspunkt
     *
     * @return ParentAssignmentEntity
     */
    public function setTidspunkt($tidspunkt)
    {
        $this->tidspunkt = $tidspunkt;

        return $this;
    }

    /**
     * Get tidspunkt.
     *
     * @return \DateTime
     */
    public function getTidspunkt()
    {
        return $this->tidspunkt;
    }
}
