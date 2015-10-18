<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="survey")
 */
class Survey
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Semester")
     */
    protected $semester;

    /**
     * @ORM\ManyToOne(targetEntity="SurveySchema")
     * @ORM\JoinColumn(name="schema_id", referencedColumnName="id")
     */
    protected $surveySchema; // Bidirectional, may turn out to be unidirectional

    /**
     * @ORM\OneToOne(targetEntity="Application", inversedBy="survey")
     * @ORM\JoinColumn(name="application_id", referencedColumnName="id")
     */
    protected $application;

    /**
     * @ORM\OneToMany(targetEntity="SurveyQuestion", mappedBy="survey", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    protected $surveyQuestions;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->surveyQuestions = new \Doctrine\Common\Collections\ArrayCollection();
    }

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
     * Set surveySchema
     *
     * @param \AppBundle\Entity\SurveySchema $surveySchema
     * @return Survey
     */
    public function setSurveySchema(\AppBundle\Entity\SurveySchema $surveySchema = null)
    {
        $this->surveySchema = $surveySchema;

        return $this;
    }

    /**
     * Get surveySchema
     *
     * @return \AppBundle\Entity\SurveySchema
     */
    public function getSurveySchema()
    {
        return $this->surveySchema;
    }


    /**
     * Set application
     *
     * @param \AppBundle\Entity\Application $application
     * @return Survey
     */
    public function setApplication(\AppBundle\Entity\Application $application = null)
    {
        $this->application = $application;

        return $this;
    }

    /**
     * Get application
     *
     * @return \AppBundle\Entity\Application
     */
    public function getApplication()
    {
        return $this->application;
    }

    /**
     * Add surveyAnswers
     *
     * @param \AppBundle\Entity\SurveyAnswer $surveyAnswers
     * @return Survey
     */
    public function addSurveyAnswer(\AppBundle\Entity\SurveyAnswer $surveyAnswers)
    {
        $this->surveyAnswers[] = $surveyAnswers;

        return $this;
    }

    /**
     * Remove surveyAnswers
     *
     * @param \AppBundle\Entity\SurveyAnswer $surveyAnswers
     */
    public function removeSurveyAnswer(\AppBundle\Entity\SurveyAnswer $surveyAnswers)
    {
        $this->surveyAnswers->removeElement($surveyAnswers);
    }

    /**
     * Get surveyAnswers
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSurveyAnswers()
    {
        return $this->surveyAnswers;
    }

    /**
     * Set surveyed
     *
     * @param boolean $surveyed
     * @return Survey
     */
    public function setSurveyed($surveyed)
    {
        $this->surveyed = $surveyed;

        return $this;
    }

    /**
     * Get surveyed
     *
     * @return boolean 
     */
    public function getSurveyed()
    {
        return $this->surveyed;
    }

    /**
     * Is the given User the surveyer of this Survey?
     *
     * @param User $user
     * @return boolean
     */
    public function isSurveyer(User $user = null)
    {
        return $user && $user->getId() == $this->getSurveyer()->getId();
    }


    /**
     * Set scheduled
     *
     * @param \DateTime $scheduled
     * @return Survey
     */
    public function setScheduled($scheduled)
    {
        $this->scheduled = $scheduled;

        return $this;
    }

    /**
     * Get scheduled
     *
     * @return \DateTime 
     */
    public function getScheduled()
    {
        return $this->scheduled;
    }

    /**
     * Set conducted
     *
     * @param \DateTime $conducted
     * @return Survey
     */
    public function setConducted($conducted)
    {
        $this->conducted = $conducted;

        return $this;
    }

    /**
     * Get conducted
     *
     * @return \DateTime 
     */
    public function getConducted()
    {
        return $this->conducted;
    }
}
