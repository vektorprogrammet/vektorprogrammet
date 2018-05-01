<?php

namespace AppBundle\Entity;

use AppBundle\Validator\Constraints as CustomAssert;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as JMS;

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
     * @Assert\NotBlank(message="Dette feltet kan ikke være blankt.")
     * @CustomAssert\UniqueCompanyEmail
     * @CustomAssert\VektorEmail
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
     * @ORM\Column(type="boolean", options={"default"=true})
     */
    protected $active;

    /**
     * @return bool
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * @param bool $active
     */
    public function setActive(bool $active)
    {
        $this->active = $active;
    }


    /**
     * @var TeamMembership[]
     * @ORM\OneToMany(targetEntity="TeamMembership", mappedBy="team")
     */
    private $teamMemberships;

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
        $this->active = true;
    }

    public function __toString()
    {
        return (string) $this->getName();
    }

    public function getType()
    {
        return 'team';
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
     *
     * @return $this|\AppBundle\Entity\Team
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
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
     *
     * @return \AppBundle\Entity\Team
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
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
     *
     * @return \AppBundle\Entity\Team
     */
    public function setShortDescription($shortDescription)
    {
        $this->shortDescription = $shortDescription;

        return $this;
    }

    /**
     * @return TeamMembership[]
     */
    public function getTeamMemberships()
    {
        return $this->teamMemberships;
    }

    /**
     * @return TeamMembership[]
     */
    public function getActiveTeamMemberships()
    {
        $histories = [];

        foreach ($this->teamMemberships as $wh) {
            $semester = $wh->getUser()->getDepartment()->getCurrentOrLatestSemester();
            if ($semester !== null && $wh->isActiveInSemester($semester)) {
                $histories[] = $wh;
            }
        }

        return $histories;
    }

    /**
     * @return User[]
     */
    public function getActiveUsers()
    {
        $activeUsers = [];

        foreach ($this->getActiveTeamMemberships() as $activeTeamMembership) {
            if (!in_array($activeTeamMembership->getUser(), $activeUsers)) {
                $activeUsers[] = $activeTeamMembership->getUser();
            }
        }

        return $activeUsers;
    }
}
