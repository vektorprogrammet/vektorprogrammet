<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SubstitutePosition
 *
 * @ORM\Table()
 * @ORM\Entity
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
     * @ORM\Column(name="comment", type="string", length=5000)
     */
    private $comment;

    /**
     * @var Application
     *
     * @ORM\OneToOne(targetEntity="Application", mappedBy="substitutePosition")
     */
    private $application;

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
}
