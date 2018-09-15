<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="assistant_history")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\AssistantHistoryRepository")
 */
class AssistantHistory
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="assistantHistories")
     * @ORM\JoinColumn(onDelete="CASCADE")
     **/
    protected $user;

    /**
     * @ORM\ManyToOne(targetEntity="Semester")
     * @ORM\JoinColumn(onDelete="SET NULL")
     * @Assert\NotBlank(message="Dette feltet kan ikke være tomt.")
     **/
    protected $semester;

    /**
     * @deprecated
     *
     * @ORM\Column(type="string", options={"default":0})
     * @Assert\NotBlank(message="Dette feltet kan ikke være tomt.")
     */
    protected $extraWorkDays;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Assert\NotBlank(message="Dette feltet kan ikke være tomt.")
     */
    protected $bolk;

    /**
     * @var WorkDay[]
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\WorkDay", mappedBy="assistantPosition")
     */
    private $workDays;

    /**
     * AssistantHistory constructor.
     */
    public function __construct()
    {
        $this->extraWorkDays = 0;
    }


    public function activeInGroup($group): bool
    {
        return strpos($this->bolk, "Bolk $group") !== false;
    }

    /**
     * Set user.
     *
     * @param \AppBundle\Entity\User $user
     *
     * @return AssistantHistory
     */
    public function setUser(\AppBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user.
     *
     * @return \AppBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set semester.
     *
     * @param \AppBundle\Entity\Semester $semester
     *
     * @return AssistantHistory
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

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    public function __toString()
    {
        return (string) $this->getId();
    }

    /**
     * Set workdays.
     *
     * @param string $extraWorkDays
     *
     * @return AssistantHistory
     */
    public function setExtraWorkDays($extraWorkDays)
    {
        $this->extraWorkDays = $extraWorkDays;

        return $this;
    }

    /**
     * Get workdays.
     *
     * @return string
     */
    public function getExtraWorkDays()
    {
        return $this->extraWorkDays;
    }

    /**
     * @return string
     */
    public function getBolk()
    {
        return $this->bolk;
    }

    /**
     * @param string $bolk
     */
    public function setBolk($bolk)
    {
        $this->bolk = $bolk;
    }

    /**
     * @return WorkDay[]
     */
    public function getWorkDays()
    {
        return $this->workDays;
    }

    /**
     * @param WorkDay[] $workDays
     *
     * @return AssistantHistory
     */
    public function setWorkDays(array $workDays): AssistantHistory
    {
        $this->workDays = $workDays;
        return $this;
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
