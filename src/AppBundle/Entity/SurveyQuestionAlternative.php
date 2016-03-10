<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity
 * @ORM\Table(name="survey_question_alternative")
 */
class SurveyQuestionAlternative
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank(message="Dette feltet kan ikke være tomt.")
     */
    protected $alternative;

    /**
     * @ORM\ManyToOne(targetEntity="SurveyQuestion", inversedBy="alternatives")
     * @ORM\JoinColumn(name="question_id", referencedColumnName="id")
     */
    protected $surveyQuestion;

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
     * Set alternative
     *
     * @param string $alternative
     * @return SurveyQuestionAlternative
     */
    public function setAlternative($alternative)
    {
        $this->alternative = $alternative;

        return $this;
    }

    /**
     * Get alternative
     *
     * @return string 
     */
    public function getAlternative()
    {
        return $this->alternative;
    }

    /**
     * Set surveyQuestion
     *
     * @param \AppBundle\Entity\SurveyQuestion $surveyQuestion
     * @return SurveyQuestionAlternative
     */
    public function setSurveyQuestion(\AppBundle\Entity\SurveyQuestion $surveyQuestion = null)
    {
        $this->surveyQuestion = $surveyQuestion;

        return $this;
    }

    /**
     * Get surveyQuestion
     *
     * @return \AppBundle\Entity\SurveyQuestion 
     */
    public function getSurveyQuestion()
    {
        return $this->surveyQuestion;
    }
}
