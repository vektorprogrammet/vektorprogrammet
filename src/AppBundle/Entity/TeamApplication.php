<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="team_application")
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
     * @ORM\Column(type="string", length=45)
     * @Assert\NotBlank(message="Dette feltet kan ikke være tomt.")
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=45, unique=true)
     * @Assert\NotBlank(message="Dette feltet kan ikke være tomt.")
     * @Assert\Email(message="Ikke gyldig e-post.")
     */
    private $email;

    /**
     * @ORM\Column(type="text")
     */
    private $applicationText;

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
     * @return mixed
     */
    public function getApplicationText()
    {
        return $this->applicationText;
    }

    /**
     * @param mixed $applicationText
     */
    public function setApplicationText($applicationText)
    {
        $this->applicationText = $applicationText;
    }

}
