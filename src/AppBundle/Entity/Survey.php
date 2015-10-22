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
     * @ORM\Column(type="string")
     */
    protected $name;

    /**
     * @ORM\ManyToMany(targetEntity="SurveyQuestion", cascade={"persist"})
     * @ORM\JoinTable(name="survey_surveys_questions",
     *      joinColumns={@ORM\JoinColumn(name="survey_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="question_id", referencedColumnName="id")}
     *      )
     **/
    protected $surveyQuestions;

    /**
     * @return mixed
     */
    public function getSurveyAnswers()
    {
        return $this->surveyAnswers;
    }

    /**
     * @param mixed $surveyAnswers
     */
    public function setSurveyAnswers($surveyAnswers)
    {
        $this->surveyAnswers = $surveyAnswers;
    }
    /**
     * @param mixed $name
     */

    /**
     * @ORM\OneToMany(targetEntity="SurveyAnswer", mappedBy="survey", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    protected $surveyAnswers;

    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @param mixed $semester
     */
    public function setSemester($semester)
    {
        $this->semester = $semester;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getSemester()
    {
        return $this->semester;
    }

    /**
     * @return mixed
     */
    public function getSurveyQuestions()
    {
        return $this->surveyQuestions;
    }



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

}
