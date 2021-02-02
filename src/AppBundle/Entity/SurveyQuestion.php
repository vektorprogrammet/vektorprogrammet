<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="survey_question")
 */
class SurveyQuestion implements JsonSerializable
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
    protected $question;

    /**
     * @ORM\Column(type="boolean")
     * @Assert\NotBlank(message="Dette feltet kan ikke være tomt.")
     */
    protected $optional;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $help;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank(message="Dette feltet kan ikke være tomt.")
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
     * @return bool
     */
    public function getOptional()
    {
        return $this->optional;
    }

    /**
     * @param bool $optional
     */
    public function setOptional($optional)
    {
        $this->optional = $optional;
    }

    /**
     * @return SurveyAnswer[]
     */
    public function getAnswers()
    {
        return $this->answers;
    }

    /**
     * @param SurveyAnswer[] $answers
     */
    public function setAnswers($answers)
    {
        $this->answers = $answers;
    }

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
     * Set question.
     *
     * @param string $question
     *
     * @return SurveyQuestion
     */
    public function setQuestion($question)
    {
        $this->question = $question;

        return $this;
    }

    /**
     * Get question.
     *
     * @return string
     */
    public function getQuestion()
    {
        return $this->question;
    }

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->alternatives = new ArrayCollection();
        $this->optional = false;
    }

    /**
     * Set help.
     *
     * @param string $help
     *
     * @return SurveyQuestion
     */
    public function setHelp($help)
    {
        $this->help = $help;

        return $this;
    }

    /**
     * Get help.
     *
     * @return string
     */
    public function getHelp()
    {
        return $this->help;
    }

    /**
     * Set type.
     *
     * @param string $type
     *
     * @return SurveyQuestion
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Add alternatives.
     *
     * @param SurveyQuestionAlternative $alternatives
     *
     * @return SurveyQuestion
     */
    public function addAlternative(SurveyQuestionAlternative $alternatives)
    {
        $this->alternatives[] = $alternatives;

        $alternatives->setSurveyQuestion($this);

        return $this;
    }

    /**
     * Remove alternatives.
     *
     * @param SurveyQuestionAlternative $alternatives
     */
    public function removeAlternative(SurveyQuestionAlternative $alternatives)
    {
        $this->alternatives->removeElement($alternatives);

        $alternatives->setSurveyQuestion(null);
    }

    /**
     * Get alternatives.
     *
     * @return Collection
     */
    public function getAlternatives()
    {
        return $this->alternatives;
    }

    public function __clone()
    {
        $this->id = null;
        $this->alternatives = new ArrayCollection();
        $this->answers = new ArrayCollection();
    }

    /**
     * Specify data which should be serialized to JSON.
     *
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     *
     * @return mixed data which can be serialized by <b>json_encode</b>,
     *               which is a value of any type other than a resource
     *
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        return array('question_id' => $this->id, 'question_label' => $this->question, 'alternatives' => $this->alternatives->toArray());
    }
}
