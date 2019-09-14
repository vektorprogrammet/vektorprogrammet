<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * Feedback
 *
 * @ORM\Table(name="feedback")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\FeedbackRepository")
 */
class Feedback
{
    const TYPE_QUESTION = 'question';
    const TYPE_ERROR = 'error';
    const TYPE_FEATURE_REQUEST = 'feature_request';
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @Assert\NotBlank(message="Dette feltet kan ikke være tomt.")
     * @ORM\Column(name="title", type="string", length=55)
     */
    private $title;

    /**
     * @var string
     *
     * @Assert\NotBlank(message="Dette feltet kan ikke være tomt.")
     * @Assert\Length(max="500", maxMessage="Maks 500 tegn", min="10", minMessage="Minimum 10 tegn")
     * @ORM\Column(name="description", type="string", length=500)
     */
    private $description;
    /**
     * @var string
     *
     * @Assert\NotNull(message="Dette feltet kan ikke være tomt.")
     * @ORM\Column(name="type", type="string", length=45)
     */
    private $type;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $user;



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
     * Set title.
     *
     * @param string $title
     *
     * @return Feedback
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set type.
     *
     * @param string $type
     *
     * @return Feedback
     */
    public function setType($type)
    {
        $this->type= $type;

        return $this;
    }

    /**
     * Get type.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set description.
     *
     * @param string $description
     *
     * @return Feedback
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set user.
     *
     * @param \AppBundle\Entity\User $user
     * 
     * @return Feedback
     */
    public function setUser($user)
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
}
