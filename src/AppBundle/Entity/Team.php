<?php

namespace AppBundle\Entity;

use AppBundle\Validator\Constraints as CustomAssert;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="team")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\TeamRepository")
 */
class Team implements TeamInterface
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=250)
     * @Assert\NotBlank(message="Dette feltet kan ikke være tomt.")
     */
    protected $name;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Assert\Email(message="Ugyldig e-post")
     * @CustomAssert\UniqueCompanyEmail
     */
    private $email;

    /**
     * @ORM\ManyToOne(targetEntity="Department", inversedBy="teams")
     **/
    protected $department;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="string", nullable=true, name="short_description")
     * @Assert\Length(maxMessage="Maks 125 Tegn", max="125")
     */
    private $shortDescription;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $acceptApplication;

    /**
     * @var WorkHistory[]
     * @ORM\OneToMany(targetEntity="WorkHistory", mappedBy="team")
     */
    private $workHistories;

    /**
     * @return bool
     */
    public function getAcceptApplication()
    {
        return $this->acceptApplication;
    }

    /**
     * @param bool $acceptApplication
     */
    public function setAcceptApplication($acceptApplication)
    {
        $this->acceptApplication = $acceptApplication;
    }

    public function __construct()
    {
        $this->users = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function __toString()
    {
        return (string) $this->getName();
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
     * Set name.
     *
     * @param string $name
     *
     * @return Team
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set department.
     *
     * @param Department $department
     *
     * @return Team
     */
    public function setDepartment(Department $department = null)
    {
        $this->department = $department;

        return $this;
    }

    /**
     * Get department.
     *
     * @return Department
     */
    public function getDepartment()
    {
        return $this->department;
    }

    // Used for unit testing
    public function fromArray($data = array())
    {
        foreach ($data as $property => $value) {
            $method = "set{$property}";
            $this->$method($value);
        }
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getShortDescription()
    {
        return $this->shortDescription;
    }

    /**
     * @param string $shortDescription
     */
    public function setShortDescription($shortDescription)
    {
        $this->shortDescription = $shortDescription;
    }

    /**
     * @return WorkHistory[]
     */
    public function getWorkHistories()
    {
        return $this->workHistories;
    }
}
