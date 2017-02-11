<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="team_application")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\TeamApplicationRepository")
 */
class TeamApplication
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
    private $name;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank(message="Dette feltet kan ikke være tomt.")
     * @Assert\Email(message="Ikke gyldig e-post.")
     */
    private $email;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank(message="Dette feltet kan ikke være tomt.")
     */
    private $fieldOfStudy;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank(message="Dette feltet kan ikke være tomt.")
     */
    private $yearOfStudy;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank(message="Dette feltet kan ikke være tomt.")
     */
    private $motivationText;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank(message="Dette feltet kan ikke være tomt.")
     */
    private $biography;

    /**
     * @ORM\ManyToOne(targetEntity="Team")
     */
    private $team;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return Team
     */
    public function getTeam()
    {
        return $this->team;
    }

    /**
     * @param Team $team
     */
    public function setTeam(Team $team)
    {
        $this->team = $team;
    }

    /**
     * @return FieldOfStudy
     */
    public function getFieldOfStudy()
    {
        return $this->fieldOfStudy;
    }

    /**
     * @param mixed $fieldOfStudy
     */
    public function setFieldOfStudy($fieldOfStudy)
    {
        $this->fieldOfStudy = $fieldOfStudy;
    }

    /**
     * @return mixed
     */
    public function getMotivationText()
    {
        return $this->motivationText;
    }

    /**
     * @param mixed $motivationText
     */
    public function setMotivationText($motivationText)
    {
        $this->motivationText = $motivationText;
    }

    /**
     * @return mixed
     */
    public function getBiography()
    {
        return $this->biography;
    }

    /**
     * @param mixed $biography
     */
    public function setBiography($biography)
    {
        $this->biography = $biography;
    }

    /**
     * @return mixed
     */
    public function getYearOfStudy()
    {
        return $this->yearOfStudy;
    }

    /**
     * @param mixed $yearOfStudy
     */
    public function setYearOfStudy($yearOfStudy)
    {
        $this->yearOfStudy = $yearOfStudy;
    }
}
