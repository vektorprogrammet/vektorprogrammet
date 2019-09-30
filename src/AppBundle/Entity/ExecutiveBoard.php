<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="executive_board")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\ExecutiveBoardRepository")
 */
class ExecutiveBoard implements TeamInterface
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
     */
    private $email;

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
     * @var ExecutiveBoardMembership[]
     * @ORM\OneToMany(targetEntity="ExecutiveBoardMembership", mappedBy="board")
     */
    private $boardMemberships;

    public function __toString()
    {
        return (string) $this->getName();
    }

    public function getType()
    {
        return 'executive_board';
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
     * @return ExecutiveBoard
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
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     *
     * @return $this|\AppBundle\Entity\ExecutiveBoard
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
     * @return \AppBundle\Entity\ExecutiveBoard
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
     * @return \AppBundle\Entity\ExecutiveBoard
     */
    public function setShortDescription($shortDescription)
    {
        $this->shortDescription = $shortDescription;

        return $this;
    }

    /**
     * @return ExecutiveBoardMembership[]
     */
    public function getBoardMemberships()
    {
        return $this->boardMemberships;
    }

    /**
     * @return \AppBundle\Entity\ExecutiveBoardMembership[]
     */
    public function getTeamMemberships()
    {
        return $this->boardMemberships;
    }

    /**
     * @return bool
     */
    public function getAcceptApplication()
    {
        return false;
    }

    /**
     * @return bool
     */
    public function getAcceptApplicationAndDeadline()
    {
        return false;
    }

    /**
     * @return ExecutiveBoardMembership[]
     */
    public function getActiveTeamMemberships()
    {
        $activeTeamMemberships = [];

        foreach ($this->getTeamMemberships() as $teamMembership) {
            if ($teamMembership->isActive()) {
                $activeTeamMemberships[] = $teamMembership;
            }
        }

        return $activeTeamMemberships;
    }

    /**
     * @return User[]
     */
    public function getActiveUsers()
    {
        $activeUsers = [];

        foreach ($this->getActiveTeamMemberships() as $activeExecutiveBoardHistory) {
            if (!in_array($activeExecutiveBoardHistory->getUser(), $activeUsers)) {
                $activeUsers[] = $activeExecutiveBoardHistory->getUser();
            }
        }

        return $activeUsers;
    }
}
