<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\SurveyRepository")
 * @ORM\Table(name="survey_pupil")
 */

class SurveyPupil
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="School")
     * @ORM\JoinColumn(name="school_id", referencedColumnName="id")
     */
    private $school;

    /**
     * @ORM\Column(type="integer")
     */
    private $question1;

    /**
     * @ORM\Column(type="string")
     */
    private $question2;

    /**
     * @ORM\Column(type="string")
     */
    private $question3;

    /**
     * @ORM\Column(type="string")
     */
    private $question4;

    /**
     * @ORM\Column(type="string")
     */
    private $question5;

    /**
     * @ORM\Column(type="string")
     */
    private $question6;

    /**
     * @ORM\Column(type="string")
     */
    private $question7;

    /**
     * @ORM\Column(type="string")
     */
    private $question8;

    /**
     * @ORM\Column(type="string")
     */
    private $question9;

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
     * Set question1
     *
     * @param string $question1
     * @return Survey
     */
    public function setQuestion1($question1)
    {
        $this->question1 = $question1;

        return $this;
    }

    /**
     * Get question1
     *
     * @return string 
     */
    public function getQuestion1()
    {
        return $this->question1;
    }

    /**
     * Set question2
     *
     * @param string $question2
     * @return Survey
     */
    public function setQuestion2($question2)
    {
        $this->question2 = $question2;

        return $this;
    }

    /**
     * Get question2
     *
     * @return string 
     */
    public function getQuestion2()
    {
        return $this->question2;
    }

    /**
     * Set question3
     *
     * @param string $question3
     * @return Survey
     */
    public function setQuestion3($question3)
    {
        $this->question3 = $question3;

        return $this;
    }

    /**
     * Get question3
     *
     * @return string 
     */
    public function getQuestion3()
    {
        return $this->question3;
    }

    /**
     * Set question4
     *
     * @param string $question4
     * @return Survey
     */
    public function setQuestion4($question4)
    {
        $this->question4 = $question4;

        return $this;
    }

    /**
     * Get question4
     *
     * @return string 
     */
    public function getQuestion4()
    {
        return $this->question4;
    }

    /**
     * Set question5
     *
     * @param string $question5
     * @return Survey
     */
    public function setQuestion5($question5)
    {
        $this->question5 = $question5;

        return $this;
    }

    /**
     * Get question5
     *
     * @return string 
     */
    public function getQuestion5()
    {
        return $this->question5;
    }

    /**
     * Set school
     *
     * @param \AppBundle\Entity\School $school
     * @return Survey
     */
    public function setSchool(\AppBundle\Entity\School $school = null)
    {
        $this->school = $school;

        return $this;
    }

    /**
     * Get school
     *
     * @return \AppBundle\Entity\School
     */
    public function getSchool()
    {
        return $this->school;
    }

    /**
     * Set question6
     *
     * @param string $question6
     * @return Survey
     */
    public function setQuestion6($question6)
    {
        $this->question6 = $question6;

        return $this;
    }

    /**
     * Get question6
     *
     * @return string 
     */
    public function getQuestion6()
    {
        return $this->question6;
    }

    /**
     * Set question7
     *
     * @param string $question7
     * @return Survey
     */
    public function setQuestion7($question7)
    {
        $this->question7 = $question7;

        return $this;
    }

    /**
     * Get question7
     *
     * @return string 
     */
    public function getQuestion7()
    {
        return $this->question7;
    }

    /**
     * Set question8
     *
     * @param string $question8
     * @return Survey
     */
    public function setQuestion8($question8)
    {
        $this->question8 = $question8;

        return $this;
    }

    /**
     * Get question8
     *
     * @return string 
     */
    public function getQuestion8()
    {
        return $this->question8;
    }

    /**
     * Set question9
     *
     * @param string $question9
     * @return Survey
     */
    public function setQuestion9($question9)
    {
        $this->question9 = $question9;

        return $this;
    }

    /**
     * Get question9
     *
     * @return string 
     */
    public function getQuestion9()
    {
        return $this->question9;
    }

}
