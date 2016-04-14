<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="survey")
 */
class Survey implements \JsonSerializable
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Semester")
     * @Assert\Valid
     */
    protected $semester;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank(message="Dette feletet kan ikke være tomt.")
     */
    protected $name;

    /**
     * @var SurveyQuestion[]
     *
     * @ORM\ManyToMany(targetEntity="SurveyQuestion", cascade={"persist"})
     * @ORM\JoinTable(name="survey_surveys_questions",
     *      joinColumns={@ORM\JoinColumn(name="survey_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="question_id", referencedColumnName="id")}
     *      )
     * @Assert\Valid
     **/
    protected $surveyQuestions;

    protected $totalAnswered;

    /**
     * @return mixed
     */
    public function getTotalAnswered()
    {
        return $this->totalAnswered;
    }

    /**
     * @param mixed $totalAnswered
     */
    public function setTotalAnswered($totalAnswered)
    {
        $this->totalAnswered = $totalAnswered;
    }


    /**
     * @param mixed $name
     */
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

    public function __clone() {
        $this->id = null;
        $this->semester = null;
        $this->surveyQuestions = new \Doctrine\Common\Collections\ArrayCollection();
        $this->totalAnswered = 0;
    }

    public function addSurveyQuestion(\AppBundle\Entity\SurveyQuestion $surveyQuestion)
    {
        $this->surveyQuestions[] = $surveyQuestion;

        return $this;
    }

    /**
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    function jsonSerialize(){
        $ret = array('questions' => array());
        foreach($this->surveyQuestions as $q){
            if(!$q->getOptional() && ($q->getType() == "radio" || $q->getType() == "list")){
                $ret['questions'][] = $q;
            }
        }
        return $ret;
    }
}
