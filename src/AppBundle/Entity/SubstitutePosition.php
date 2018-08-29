<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SubstitutePosition
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\SubstitutePositionRepository")
 */
class SubstitutePosition
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="comment", type="string", length=5000, nullable=true)
     */
    private $comment;

    /**
     * @var Application
     *
     * @ORM\OneToOne(targetEntity="Application", mappedBy="substitutePosition")
     */
    private $application;

    /**
     * @var Substitution[]
     *
     * @ORM\OneToMany(targetEntity="Substitution", mappedBy="substitutePosition")
     */
    private $substitutions;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set comment
     *
     * @param string $comment
     * @return SubstitutePosition
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get comment
     *
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Get application
     *
     * @return Application
     */
    public function getApplication()
    {
        return $this->application;
    }

    /**
     * Set application
     *
     * @param Application $application
     * @return SubstitutePosition
     */
    public function setApplication($application)
    {
        $this->application = $application;

        return $this;
    }

    /**
     * Get substitutions
     *
     * @return Substitution[]
     */
    public function getSubstitutions()
    {
        return $this->substitutions;
    }

    /**
     * Set substitutions
     *
     * @param Substitution[] $substitutions
     *
     * @return SubstitutePosition
     */
    public function setSubstitutions($substitutions): SubstitutePosition
    {
        $this->substitutions = $substitutions;
        foreach ($substitutions as $substitution) {
            $substitution->setSubstitutePosition($this);
        }
        return $this;
    }

    /**
     * Add substitution
     *
     * @param Substitution $substitution
     *
     * @return SubstitutePosition
     */
    public function addSubstitution(Substitution $substitution) : SubstitutePosition
    {
        $this->substitutions[] = $substitution;
        $substitution->setSubstitutePosition($this);

        return $this;
    }
}
