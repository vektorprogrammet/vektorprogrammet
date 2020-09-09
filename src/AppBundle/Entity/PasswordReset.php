<?php

namespace AppBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\PasswordResetRepository")
 * @ORM\Table(name="password_reset")
 */
class PasswordReset
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user", referencedColumnName="id")
     */
    protected $user;

    /**
     * @ORM\Column(type="string")
     */
    protected $hashedResetCode;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $resetTime;

    /**
     * @var string
     */
    private $resetCode;

    /**
     * PasswordReset constructor.
     */
    public function __construct()
    {
        $this->setResetTime(new DateTime());
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
     * Set hashedResetCode.
     *
     * @param string $hashedResetCode
     *
     * @return PasswordReset
     */
    public function setHashedResetCode($hashedResetCode)
    {
        $this->hashedResetCode = $hashedResetCode;

        return $this;
    }

    /**
     * Get hashedResetCode.
     *
     * @return string
     */
    public function getHashedResetCode()
    {
        return $this->hashedResetCode;
    }

    /**
     * Set resetTime.
     *
     * @param DateTime $resetTime
     *
     * @return PasswordReset
     */
    public function setResetTime($resetTime)
    {
        $this->resetTime = $resetTime;

        return $this;
    }

    /**
     * Get resetTime.
     *
     * @return DateTime
     */
    public function getResetTime()
    {
        return $this->resetTime;
    }

    /**
     * Set user.
     *
     * @param \AppBundle\Entity\User $user
     *
     * @return PasswordReset
     */
    public function setUser(User $user = null)
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
     * @return string
     */
    public function getResetCode()
    {
        return $this->resetCode;
    }

    /**
     * @param string $resetCode
     */
    public function setResetCode($resetCode)
    {
        $this->resetCode = $resetCode;
    }
}
