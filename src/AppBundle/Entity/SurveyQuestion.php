<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

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
     */
    protected $question;


    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $help;

    /**
     * @ORM\Column(type="string")
     */
    protected $type;

    /**
     * @ORM\OneToMany(targetEntity="SurveyQuestionAlternative", mappedBy="surveyQuestion", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    protected $alternatives;

    /**
     * @ORM\ManyToMany(targetEntity="SurveyAnswer", cascade={"persist"})
     * @ORM\JoinTable(name="survey_questions_answers",
     *      joinColumns={@ORM\JoinColumn(name="question_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="answer_id", referencedColumnName="id")}
     *      )
     **/
    protected $answers;

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
