<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ParentAssignment
 *
 * @ORM\Table(name="parent_assignment")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\ParentAssignmentRepository")
 */
class ParentAssignment
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
     * @var string
     *
     * @ORM\Column(name="UniqueKey", type="string", nullable=True)
     */
    private $uniqueKey;


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
     * @return ParentAssignment
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
     * @return ParentAssignment
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
     * @return ParentAssignment
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

    /**
     * @param string $uniqueKey
     */
    public function setUniqueKey(string $uniqueKey): void
    {
        $this->uniqueKey = $uniqueKey;
    }

    /**
     * @return string
     */
    public function getUniqueKey(): string
    {
        return $this->uniqueKey;
    }
}
