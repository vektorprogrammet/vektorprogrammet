<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity
 * @ORM\Table(name="survey_question")
 */
class SurveyQuestion
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank(message="Dette feletet kan ikke være tomt.")
     */
    protected $question;


    /**
     * @ORM\Column(type="boolean")
     * @Assert\NotBlank(message="Dette feletet kan ikke være tomt.")
     */
    protected $optional;


    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $help;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank(message="Dette feletet kan ikke være tomt.")
     */
    protected $type;

    /**
     * @ORM\OneToMany(targetEntity="SurveyQuestionAlternative", mappedBy="surveyQuestion", cascade={"persist", "remove"}, orphanRemoval=true)
     * @Assert\Valid
     */
    protected $alternatives;

    /**
     * @ORM\OneToMany(targetEntity="SurveyAnswer", mappedBy="surveyQuestion", cascade={"persist", "remove"})
     **/
    protected $answers;



    /**
     * @return mixed
     */
    public function getOptional()
    {
        return $this->optional;
    }

    /**
     * @param mixed $optional
     */
    public function setOptional($optional)
    {
        $this->optional = $optional;
    }

    /**
     * @return mixed
     */
    public function getAnswers()
    {
        return $this->answers;
    }

    /**
     * @param mixed $answers
     */
    public function setAnswers($answers)
    {
        $this->answers = $answers;
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
     * Set question
     *
     * @param string $question
     * @return SurveyQuestion
     */
    public function setQuestion($question)
    {
        $this->question = $question;

        return $this;
    }

    /**
     * Get question
     *
     * @return string 
     */
    public function getQuestion()
    {
        return $this->question;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->alternatives = new \Doctrine\Common\Collections\ArrayCollection();
        $this->optional = false;
    }

    /**
     * Set help
     *
     * @param string $help
     * @return SurveyQuestion
     */
    public function setHelp($help)
    {
        $this->help = $help;

        return $this;
    }

    /**
     * Get help
     *
     * @return string 
     */
    public function getHelp()
    {
        return $this->help;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return SurveyQuestion
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Add alternatives
     *
     * @param \AppBundle\Entity\SurveyQuestionAlternative $alternatives
     * @return SurveyQuestion
     */
    public function addAlternative(\AppBundle\Entity\SurveyQuestionAlternative $alternatives)
    {
        $this->alternatives[] = $alternatives;

        $alternatives->setSurveyQuestion($this);

        return $this;
    }

    /**
     * Remove alternatives
     *
     * @param \AppBundle\Entity\SurveyQuestionAlternative $alternatives
     */
    public function removeAlternative(\AppBundle\Entity\SurveyQuestionAlternative $alternatives)
    {
        $this->alternatives->removeElement($alternatives);

        $alternatives->setSurveyQuestion(null);
    }

    /**
     * Get alternatives
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAlternatives()
    {
        return $this->alternatives;
    }
}
