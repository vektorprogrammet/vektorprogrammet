<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="interview_schema")
 */
class InterviewSchema
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank(message="Dette feltet kan ikke være tomt.")
     */
    protected $name;

    /**
     * @ORM\ManyToMany(targetEntity="InterviewQuestion", cascade={"persist"})
     * @ORM\JoinTable(name="interview_schemas_questions",
     *      joinColumns={@ORM\JoinColumn(name="schema_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="question_id", referencedColumnName="id")}
     *      )
     * @Assert\Valid
     **/
    protected $interviewQuestions; // Unidirectional, may turn out to be bidirectional

    /**
     * Question IDs in display order, scoped to this schema (questions may be shared).
     * @ORM\Column(name="question_order", type="json", nullable=true)
     */
    private $questionOrder;

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
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Add questions.
     *
     * @param InterviewQuestion $questions
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
     *
     * @param InterviewQuestion $questions
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
        if (!$this->questionOrder) {
            return $this->interviewQuestions;
        }

        $rank = array_flip($this->questionOrder);
        $questions = $this->interviewQuestions->toArray();
        uasort($questions, static function (InterviewQuestion $a, InterviewQuestion $b) use ($rank) {
            return ($rank[$a->getId()] ?? PHP_INT_MAX) <=> ($rank[$b->getId()] ?? PHP_INT_MAX);
        });

        return new ArrayCollection($questions);
    }

    public function setQuestionOrder(array $questions): void
    {
        $this->questionOrder = array_map(static function (InterviewQuestion $question) {
            return $question->getId();
        }, $questions);
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

    public function __toString()
    {
        return $this->getName();
    }
}
