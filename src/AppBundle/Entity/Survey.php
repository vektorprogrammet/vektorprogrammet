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
     * @ORM\Column(type="boolean")
     */
    protected $surveyed;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $scheduled;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $conducted;

    /**
     * @ORM\ManyToOne(targetEntity="SurveySchema")
     * @ORM\JoinColumn(name="schema_id", referencedColumnName="id")
     */
    protected $surveySchema; // Bidirectional, may turn out to be unidirectional

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="surveyer_id", referencedColumnName="id")
     */
    protected $surveyer; // Unidirectional, may turn out to be bidirectional

    /**
     * @ORM\OneToOne(targetEntity="Application", inversedBy="survey")
     * @ORM\JoinColumn(name="application_id", referencedColumnName="id")
     */
    protected $application;

    /**
     * @ORM\OneToMany(targetEntity="SurveyAnswer", mappedBy="survey", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    protected $surveyAnswers;

    /**
     * @ORM\OneToOne(targetEntity="SurveyScore", cascade={"persist"})
     * @ORM\JoinColumn(name="survey_score_id", referencedColumnName="id")
     */
    protected $surveyScore;

    /**
     * @ORM\OneToOne(targetEntity="SurveyPractical", cascade={"persist"})
     * @ORM\JoinColumn(name="survey_practical_id", referencedColumnName="id")
     */
    protected $surveyPractical;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->surveyAnswers = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set surveyer
     *
     * @param \AppBundle\Entity\User $surveyer
     * @return Survey
     */
    public function setSurveyer(\AppBundle\Entity\User $surveyer = null)
    {
        $this->surveyer = $surveyer;

        return $this;
    }

    /**
     * Get surveyer
     *
     * @return \AppBundle\Entity\User
     */
    public function getSurveyer()
    {
        return $this->surveyer;
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
     * Set surveyScore
     *
     * @param \AppBundle\Entity\SurveyScore $surveyScore
     * @return Survey
     */
    public function setSurveyScore(\AppBundle\Entity\SurveyScore $surveyScore = null)
    {
        $this->surveyScore = $surveyScore;

        return $this;
    }

    /**
     * Get surveyScore
     *
     * @return \AppBundle\Entity\SurveyScore
     */
    public function getSurveyScore()
    {
        return $this->surveyScore;
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
     * Set surveyPractical
     *
     * @param \AppBundle\Entity\SurveyPractical $surveyPractical
     * @return Survey
     */
    public function setSurveyPractical(\AppBundle\Entity\SurveyPractical $surveyPractical = null)
    {
        $this->surveyPractical = $surveyPractical;

        return $this;
    }

    /**
     * Get surveyPractical
     *
     * @return \AppBundle\Entity\SurveyPractical 
     */
    public function getSurveyPractical()
    {
        return $this->surveyPractical;
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
