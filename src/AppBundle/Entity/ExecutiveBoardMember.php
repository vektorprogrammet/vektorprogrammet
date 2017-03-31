<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="executive_board_member")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\ExecutiveBoardRepository")
 */
class ExecutiveBoardMember
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(onDelete="SET NULL")
     * @Assert\Valid
     **/
    private $user;


    /**
     * @var ExecutiveBoard
     * @ORM\ManyToOne(targetEntity="ExecutiveBoard")
     * @ORM\JoinColumn(onDelete="SET NULL")
     **/
    private $board;


    /**
     * @ORM\Column(type="text", nullable=true)
     * @Assert\Valid
     **/
    private $position;

    public function __toString()
    {
        return (string) $this->getId();
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
     * Set user.
     *
     * @param \AppBundle\Entity\User $user
     *
     * @return ExecutiveBoardMember
     */
    public function setUser(User $user = null)
    {
        $this->user= $user;

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
     * Set board.
     *
     * @param ExecutiveBoard $board
     *
     * @return ExecutiveBoardMember
     */
    public function setBoard(ExecutiveBoard $board = null)
    {
        $this->board = $board;

        return $this;
    }

    /**
     * Get board.
     *
     * @return ExecutiveBoard
     */
    public function getBoard()
    {
        return $this->board;
    }

    /**
     * @return string
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @param string $position
     */
    public function setPosition($position)
    {
        $this->position = $position;
    }


}
