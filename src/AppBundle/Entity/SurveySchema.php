<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="survey_schema")
 */
class SurveySchema
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
    protected $name;

    /**
     * @ORM\ManyToMany(targetEntity="SurveyQuestion", cascade={"persist"})
     * @ORM\JoinTable(name="survey_schemas_questions",
     *      joinColumns={@ORM\JoinColumn(name="schema_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="question_id", referencedColumnName="id")}
     *      )
     **/
    protected $surveyQuestions; // Unidirectional, may turn out to be bidirectional

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
     * Add questions
     *
     * @param \AppBundle\Entity\SurveyQuestion $questions
     * @return SurveySchema
     */
    public function addSurveyQuestion(\AppBundle\Entity\SurveyQuestion $questions)
    {
        $this->surveyQuestions[] = $questions;

        return $this;
    }

    /**
     * Remove questions
     *
     * @param \AppBundle\Entity\SurveyQuestion $questions
     */
    public function removeSurveyQuestion(\AppBundle\Entity\SurveyQuestion $questions)
    {
        $this->surveyQuestions->removeElement($questions);
    }

    /**
     * Get questions
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSurveyQuestions()
    {
        return $this->surveyQuestions;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return SurveySchema
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }
}
