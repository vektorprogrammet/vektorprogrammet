<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="interview_question")
 */
class InterviewQuestion
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=5000)
     * @Assert\NotBlank(message="Spørsmål: Dette feltet kan ikke være tomt.")
     * @Assert\Length(max="5000", maxMessage="Spørsmål: Maks 5000 tegn")
     */
    protected $question;

    /**
     * @ORM\Column(type="string", nullable=true, length=5000)
     */
    protected $help;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank(message="Dette feltet kan ikke være tomt.")
     */
    protected $type;

    /**
     * @ORM\OneToMany(targetEntity="InterviewQuestionAlternative", mappedBy="interviewQuestion", cascade={"persist", "remove"}, orphanRemoval=true)
     * @Assert\Valid
     */
    protected $alternatives;

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
     * Set question.
     *
     * @param string $question
     *
     * @return InterviewQuestion
     */
    public function setQuestion($question)
    {
        $this->question = $question;

        return $this;
    }

    /**
     * Get question.
     *
     * @return string
     */
    public function getQuestion()
    {
        return $this->question;
    }

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->alternatives = new ArrayCollection();
    }

    /**
     * Set help.
     *
     * @param string $help
     *
     * @return InterviewQuestion
     */
    public function setHelp($help)
    {
        $this->help = $help;

        return $this;
    }

    /**
     * Get help.
     *
     * @return string
     */
    public function getHelp()
    {
        return $this->help;
    }

    /**
     * Set type.
     *
     * @param string $type
     *
     * @return InterviewQuestion
     */
    public function setType($type)
    {
        $this->type = $type;

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
     * Add alternatives.
     *
     * @param InterviewQuestionAlternative $alternatives
     *
     * @return InterviewQuestion
     */
    public function addAlternative(InterviewQuestionAlternative $alternatives)
    {
        $this->alternatives[] = $alternatives;

        $alternatives->setInterviewQuestion($this);

        return $this;
    }

    /**
     * Remove alternatives.
     *
     * @param InterviewQuestionAlternative $alternatives
     */
    public function removeAlternative(InterviewQuestionAlternative $alternatives)
    {
        $this->alternatives->removeElement($alternatives);

        $alternatives->setInterviewQuestion(null);
    }

    /**
     * Get alternatives.
     *
     * @return Collection
     */
    public function getAlternatives()
    {
        return $this->alternatives;
    }
}
