<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\SubstituteRepository")
 * @ORM\Table(name="substitute")
 */
class Substitute
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=250)
     */
    protected $firstName;

    /**
     * @ORM\Column(type="string", length=250)
     */
    protected $lastName;

    /**
     * @ORM\Column(type="string", length=250)
     */
    protected $phone;

    /**
     * @ORM\Column(type="string", length=250)
     */
    protected $email;

    /**
     * @ORM\Column(type="string" , length=20)
     */
    protected $yearOfStudy;

    /**
     * @ORM\ManyToOne(targetEntity="FieldOfStudy")
     **/
    protected $fieldOfStudy;

    /**
     * @ORM\ManyToOne(targetEntity="Semester")
     **/
    protected $semester;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    protected $monday;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    protected $tuesday;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    protected $wednesday;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    protected $thursday;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    protected $friday;

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
     * Set firstName.
     *
     * @param string $firstName
     *
     * @return Substitute
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get firstName.
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set lastName.
     *
     * @param string $lastName
     *
     * @return Substitute
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get lastName.
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set phone.
     *
     * @param string $phone
     *
     * @return Substitute
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone.
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set email.
     *
     * @param string $email
     *
     * @return Substitute
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email.
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set yearOfStudy.
     *
     * @param string $yearOfStudy
     *
     * @return Substitute
     */
    public function setYearOfStudy($yearOfStudy)
    {
        $this->yearOfStudy = $yearOfStudy;

        return $this;
    }

    /**
     * Get yearOfStudy.
     *
     * @return string
     */
    public function getYearOfStudy()
    {
        return $this->yearOfStudy;
    }

    /**
     * Set monday.
     *
     * @param string $monday
     *
     * @return Substitute
     */
    public function setMonday($monday)
    {
        $this->monday = $monday;

        return $this;
    }

    /**
     * Get monday.
     *
     * @return string
     */
    public function getMonday()
    {
        return $this->monday;
    }

    /**
     * Set tuesday.
     *
     * @param string $tuesday
     *
     * @return Substitute
     */
    public function setTuesday($tuesday)
    {
        $this->tuesday = $tuesday;

        return $this;
    }

    /**
     * Get tuesday.
     *
     * @return string
     */
    public function getTuesday()
    {
        return $this->tuesday;
    }

    /**
     * Set wednesday.
     *
     * @param string $wednesday
     *
     * @return Substitute
     */
    public function setWednesday($wednesday)
    {
        $this->wednesday = $wednesday;

        return $this;
    }

    /**
     * Get wednesday.
     *
     * @return string
     */
    public function getWednesday()
    {
        return $this->wednesday;
    }

    /**
     * Set thursday.
     *
     * @param string $thursday
     *
     * @return Substitute
     */
    public function setThursday($thursday)
    {
        $this->thursday = $thursday;

        return $this;
    }

    /**
     * Get thursday.
     *
     * @return string
     */
    public function getThursday()
    {
        return $this->thursday;
    }

    /**
     * Set friday.
     *
     * @param string $friday
     *
     * @return Substitute
     */
    public function setFriday($friday)
    {
        $this->friday = $friday;

        return $this;
    }

    /**
     * Get friday.
     *
     * @return string
     */
    public function getFriday()
    {
        return $this->friday;
    }

    /**
     * Set fieldOfStudy.
     *
     * @param \AppBundle\Entity\FieldOfStudy $fieldOfStudy
     *
     * @return Substitute
     */
    public function setFieldOfStudy(\AppBundle\Entity\FieldOfStudy $fieldOfStudy = null)
    {
        $this->fieldOfStudy = $fieldOfStudy;

        return $this;
    }

    /**
     * Get fieldOfStudy.
     *
     * @return \AppBundle\Entity\FieldOfStudy
     */
    public function getFieldOfStudy()
    {
        return $this->fieldOfStudy;
    }

    /**
     * Set semester.
     *
     * @param \AppBundle\Entity\Semester $semester
     *
     * @return Substitute
     */
    public function setSemester(\AppBundle\Entity\Semester $semester = null)
    {
        $this->semester = $semester;

        return $this;
    }

    /**
     * Get semester.
     *
     * @return \AppBundle\Entity\Semester
     */
    public function getSemester()
    {
        return $this->semester;
    }
}
