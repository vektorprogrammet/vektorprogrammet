<?php

namespace AppBundle\Entity;

use AppBundle\Validator\Constraints as CustomAssert;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Table(name="team")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\TeamRepository")
 * @UniqueEntity(
 *     fields={"department", "name"},
 *     message="Et team med dette navnet finnes allerede i avdelingen.",
 * )
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
     * @Assert\NotNull(message="Avdeling kan ikke være null")
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
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $deadline;

    /**
     * Applications with team interest
     * @var Application[]
     *
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Application", mappedBy="potentialTeams")
     */
    private $potentialMembers;

    /**
     * TeamInterest entities not corresponding to any Application
     * @var TeamInterest[]
     *
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\TeamInterest", mappedBy="potentialTeams")
     */
    private $potentialApplicants;

    /**
     * @ORM\Column(type="boolean", options={"default"=true})
     */
    private $active;

    /**
     * @ORM\OneToMany(targetEntity="TeamApplication", mappedBy="team")
     */
    private $applications;

    public function isActive(): bool
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
     * @return Team
     */

    public function setAcceptApplication(bool $acceptApplication)
    {
        $this->acceptApplication = $acceptApplication;
        return $this;
    }


    public function __construct()
    {
        $this->active = true;
        $this->teamMemberships = [];
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
     * @return DateTime
     */
    public function getDeadline()
    {
        return $this->deadline;
    }

    /**
     * @param DateTime $deadline
     *
     * @return Team
     */
    public function setDeadline($deadline)
    {
        $now = new DateTime();
        if ($this->acceptApplication && $now <= $deadline) {
            $this->deadline = $deadline;
        } else {
            $this->deadline = null;
        }
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
            $semester = $wh->getUser()->getDepartment()->getCurrentOrLatestAdmissionPeriod()->getSemester();
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

    /**
     * @return Application[]
     */
    public function getPotentialMembers()
    {
        return $this->potentialMembers;
    }

    /**
     * @param Application[] $potentialMembers
     */
    public function setPotentialMembers($potentialMembers)
    {
        $this->potentialMembers = $potentialMembers;
    }

    /**
     * @return TeamInterest[]
     */
    public function getPotentialApplicants()
    {
        return $this->potentialApplicants;
    }

    /**
     * @param TeamInterest[] $potentialApplicants
     *
     * @return Team
     */
    public function setPotentialApplicants($potentialApplicants)
    {
        $this->potentialApplicants = $potentialApplicants;

        return $this;
    }

    public function getNumberOfPotentialMembersAndApplicantsInSemester($semester)
    {
        $array = array_merge($this->potentialApplicants->toArray(), $this->potentialMembers->toArray());
        $array = array_filter($array, function (DepartmentSemesterInterface $a) use ($semester) {
            return $a->getSemester() === $semester;
        });
        return count($array);
    }

    /**
     * @return TeamApplication[]
     */
    public function getApplications()
    {
        return $this->applications;
    }

    /**
     * @param TeamApplication $applications
     */
    public function setApplications(TeamApplication $applications): void
    {
        $this->applications = $applications;
    }

    /**
     * @return bool
     */
    public function getAcceptApplicationAndDeadline()
    {
        $now = new DateTime();
        return (($this->acceptApplication && $now < $this->deadline) || ($this->acceptApplication && $this->deadline === null));
    }
}
