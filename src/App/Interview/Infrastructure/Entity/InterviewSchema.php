<?php

namespace App\Interview\Infrastructure\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\Table(name: 'interview_schema')]
class InterviewSchema implements \Stringable
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    protected $id;

    #[ORM\Column(type: 'string')]
    #[Assert\NotBlank(message: 'Dette feltet kan ikke være tomt.')]
    protected $name;
    #[ORM\ManyToMany(targetEntity: InterviewQuestion::class, cascade: ['persist'])]
    #[ORM\JoinTable(name: 'interview_schemas_questions')]
    #[ORM\JoinColumn(name: 'schema_id', referencedColumnName: 'id')]
    #[ORM\InverseJoinColumn(name: 'question_id', referencedColumnName: 'id')]
    #[Assert\Valid]
    protected $interviewQuestions; // Unidirectional, may turn out to be bidirectional

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->interviewQuestions = new ArrayCollection();
    }

    /**
     * Get id.
     *
     * @return int|null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Add questions.
     *
     * @return InterviewSchema
     */
    public function addInterviewQuestion(InterviewQuestion $questions)
    {
        $this->interviewQuestions[] = $questions;

        return $this;
    }

    /**
     * Remove questions.
     */
    public function removeInterviewQuestion(InterviewQuestion $questions)
    {
        $this->interviewQuestions->removeElement($questions);
    }

    /**
     * Get questions.
     *
     * @return Collection
     */
    public function getInterviewQuestions()
    {
        return $this->interviewQuestions;
    }

    /**
     * Set name.
     *
     * @param string $name
     *
     * @return InterviewSchema
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

    public function __toString(): string
    {
        return $this->getName();
    }
}
