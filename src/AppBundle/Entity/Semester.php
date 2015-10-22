<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="Semester")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\SemesterRepository")
 */
class Semester {

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
	
	/**
     * @ORM\ManyToOne(targetEntity="Department", inversedBy="semesters")
     */
    protected $department;

	/**
     * @ORM\Column(type="datetime", length=150)
     */
    protected $admission_start_date;
	
	/**
     * @ORM\Column(type="datetime", length=150)
     */
    protected $admission_end_date;
	
	/**
     * @ORM\Column(type="string", length=250)
     */
    protected $name;
	
	/**
     * @ORM\Column(type="datetime", length=150)
     */
	protected $semesterStartDate;
	
	/**
     * @ORM\Column(type="datetime", length=150)
     */
	protected $semesterEndDate;




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
     * Set admission_start_date
     *
     * @param \DateTime $admissionStartDate
     * @return Semester
     */
    public function setAdmissionStartDate($admissionStartDate)
    {
        $this->admission_start_date = $admissionStartDate;

        return $this;
    }
	
	/**
     * Set admission_start_date
     *
     * @param \DateTime $admissionStartDate
     * @return Semester
     */
    public function setAdmission_Start_Date($admissionStartDate)
    {
        $this->admission_start_date = $admissionStartDate;

        return $this;
    }

    /**
     * Get admission_start_date
     *
     * @return \DateTime 
     */
    public function getAdmissionStartDate()
    {
        return $this->admission_start_date;
    }

    /**
     * Set admission_end_date
     *
     * @param \DateTime $admissionEndDate
     * @return Semester
     */
    public function setAdmissionEndDate($admissionEndDate)
    {
        $this->admission_end_date = $admissionEndDate;

        return $this;
    }
	
	/**
     * Set admission_end_date
     *
     * @param \DateTime $admissionEndDate
     * @return Semester
     */
    public function setAdmission_End_Date($admissionEndDate)
    {
        $this->admission_end_date = $admissionEndDate;

        return $this;
    }

    /**
     * Get admission_end_date
     *
     * @return \DateTime 
     */
    public function getAdmissionEndDate()
    {
        return $this->admission_end_date;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Semester
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

    /**
     * Set department
     *
     * @param \AppBundle\Entity\Department $department
     * @return Semester
     */
    public function setDepartment(\AppBundle\Entity\Department $department = null)
    {
        $this->department = $department;

        return $this;
    }

    /**
     * Get department
     *
     * @return \AppBundle\Entity\Department
     */
    public function getDepartment()
    {
        return $this->department;
    }
	
	public function __toString()
	{
		return (string) $this->getName().' - '.$this->getDepartment(); //Fix for viewing departmentname in semesterlist.
	}

    /**
     * Set semesterStartDate
     *
     * @param \DateTime $semesterStartDate
     * @return Semester
     */
    public function setSemesterStartDate($semesterStartDate)
    {
        $this->semesterStartDate = $semesterStartDate;

        return $this;
    }

    /**
     * Get semesterStartDate
     *
     * @return \DateTime 
     */
    public function getSemesterStartDate()
    {
        return $this->semesterStartDate;
    }

    /**
     * Set semesterEndDate
     *
     * @param \DateTime $semesterEndDate
     * @return Semester
     */
    public function setSemesterEndDate($semesterEndDate)
    {
        $this->semesterEndDate = $semesterEndDate;

        return $this;
    }

    /**
     * Get semesterEndDate
     *
     * @return \DateTime 
     */
    public function getSemesterEndDate()
    {
        return $this->semesterEndDate;
    }
	
	// Used for unit testing 
	public function fromArray($data = array())
    {
        foreach ($data as $property => $value) {
            $method = "set{$property}";
            $this->$method($value);
        }
    }
}
