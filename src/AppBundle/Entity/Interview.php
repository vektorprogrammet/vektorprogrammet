<?php

namespace AppBundle\Entity;

use AppBundle\Type\InterviewStatusType;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\InterviewRepository")
 * @ORM\Table(name="interview")
 */
class Interview
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="boolean")
     */
    private $interviewed;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $scheduled;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $lastScheduleChanged;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $room;

    /**
    * @ORM\Column(type="string", length=255, nullable=true)
    * @Assert\Length(
    *     max=255,
    *     maxMessage="Campusnavn kan ikke være mer enn 255 tegn"
    * )
    */
    private $campus;

    /**
     * @ORM\Column(type="string", length=500, nullable=true)
     * @Assert\Length(
     *     max=500,
     *     maxMessage= "Linken kan ikke være mer enn 500 tegn"
     * )
     */
    private $mapLink;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $conducted;

    /**
     * @ORM\ManyToOne(targetEntity="InterviewSchema")
     * @ORM\JoinColumn(name="schema_id", referencedColumnName="id")
     */
    private $interviewSchema; // Bidirectional, may turn out to be unidirectional

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="interviews")
     * @ORM\JoinColumn(name="interviewer_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private $interviewer; // Unidirectional, may turn out to be bidirectional

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    private $coInterviewer;

    /**
     * @ORM\OneToMany(targetEntity="InterviewAnswer", mappedBy="interview", cascade={"persist", "remove"})
     * @Assert\Valid
     */
    private $interviewAnswers;

    /**
     * @var InterviewScore
     *
     * @ORM\OneToOne(targetEntity="InterviewScore", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="interview_score_id", referencedColumnName="id")
     * @Assert\Valid
     */
    private $interviewScore;

    /**
     * @ORM\Column(type="integer", nullable=false)
     *
     * @var int
     */
    private $interviewStatus;

    /**
     * @ORM\ManyToOne(targetEntity="User", cascade={"persist"})
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $user;

    /**
     * @ORM\OneToOne(targetEntity="Application", mappedBy="interview")
     */
    private $application;

    /**
     * @ORM\Column(type="string", nullable=true)
     *
     * @var string
     */
    private $responseCode;

    /**
     * @ORM\Column(type="string", nullable=true, length=2000)
     * @Assert\Length(max=2000)
     * @var string
     */
    private $cancelMessage;

    /**
     * @ORM\Column(type="string", length=2000)
     * @Assert\Length(max=2000)
     * @Assert\NotBlank(groups={"newTimeRequest"}, message="Meldingsboksen kan ikke være tom")
     *
     * @var string
     */
    private $newTimeMessage;

    /**
     * @var int
     * @ORM\Column(type="integer", options={"default": 0})
     */
    private $numAcceptInterviewRemindersSent;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->interviewAnswers = new ArrayCollection();
        $this->conducted = new DateTime();
        $this->interviewed = false;
        $this->interviewStatus = InterviewStatusType::NO_CONTACT;
        $this->newTimeMessage = "";
        $this->numAcceptInterviewRemindersSent = 0;
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
     * Set interviewSchema.
     *
     * @param InterviewSchema $interviewSchema
     *
     * @return Interview
     */
    public function setInterviewSchema(InterviewSchema $interviewSchema = null)
    {
        $this->interviewSchema = $interviewSchema;

        return $this;
    }

    /**
     * Get interviewSchema.
     *
     * @return InterviewSchema
     */
    public function getInterviewSchema()
    {
        return $this->interviewSchema;
    }

    /**
     * Get coInterviewer.
     *
     * @return User
     */
    public function getCoInterviewer()
    {
        return $this->coInterviewer;
    }

    /**
     * Is the given User the co-interviewer of this Interview?
     *
     * @param User $user
     *
     * @return bool
     */
    public function isCoInterviewer(User $user = null)
    {
        return $user && $this->getCoInterviewer() && $user->getId() == $this->getCoInterviewer()->getId();
    }

    /**
     * Set interviewer.
     *
     * @param User $coInterviewer
     *
     * @return Interview
     */
    public function setCoInterviewer(User $coInterviewer = null)
    {
        $this->coInterviewer = $coInterviewer;

        return $this;
    }

    /**
     * Set interviewer.
     *
     * @param User $interviewer
     *
     * @return Interview
     */
    public function setInterviewer(User $interviewer = null)
    {
        $this->interviewer = $interviewer;

        return $this;
    }

    /**
     * Get interviewer.
     *
     * @return User
     */
    public function getInterviewer()
    {
        return $this->interviewer;
    }

    /**
     * Add interviewAnswers.
     *
     * @param InterviewAnswer $interviewAnswers
     *
     * @return Interview
     */
    public function addInterviewAnswer(InterviewAnswer $interviewAnswers)
    {
        $this->interviewAnswers[] = $interviewAnswers;

        return $this;
    }

    /**
     * Remove interviewAnswers.
     *
     * @param InterviewAnswer $interviewAnswers
     */
    public function removeInterviewAnswer(InterviewAnswer $interviewAnswers)
    {
        $this->interviewAnswers->removeElement($interviewAnswers);
    }

    /**
     * Get interviewAnswers.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getInterviewAnswers()
    {
        return $this->interviewAnswers;
    }

    /**
     * Set interviewScore.
     *
     * @param InterviewScore $interviewScore
     *
     * @return Interview
     */
    public function setInterviewScore(InterviewScore $interviewScore = null)
    {
        $this->interviewScore = $interviewScore;

        return $this;
    }

    /**
     * Get interviewScore.
     *
     * @return InterviewScore
     */
    public function getInterviewScore()
    {
        return $this->interviewScore;
    }

    public function getScore()
    {
        if ($this->interviewScore === null) {
            return 0;
        }

        return $this->interviewScore->getSum();
    }

    /**
     * Set interviewed.
     *
     * @param bool $interviewed
     *
     * @return Interview
     */
    public function setInterviewed($interviewed)
    {
        $this->interviewed = $interviewed;

        return $this;
    }

    /**
     * Get interviewed.
     *
     * @return bool
     */
    public function getInterviewed()
    {
        return $this->interviewed;
    }

    /**
     * @return bool
     */
    public function getCancelled()
    {
        return $this->isCancelled();
    }

    /**
     * @param bool $cancelled
     */
    public function setCancelled($cancelled)
    {
        if ($cancelled === true) {
            $this->cancel();
        } else {
            $this->acceptInterview();
        }
    }

    /**
     * @return string
     */
    public function getRoom()
    {
        return $this->room;
    }

    /**
     * @param string $room
     */
    public function setRoom($room)
    {
        $this->room = $room;
    }

    /**
     * @return string
     */
    public function getCampus()
    {
        return $this->campus;
    }

    /**
     * @param string $campus
     *
     * @return Interview
     */
    public function setCampus($campus)
    {
        $this->campus = $campus;
        return $this;
    }

    /**
     * @return string
     */
    public function getMapLink()
    {
        return $this->mapLink;
    }

    /**
     * @param string mapLink
     */
    public function setMapLink($mapLink)
    {
        $this->mapLink = $mapLink;
    }


    /**
     * Is the given User the interviewer of this Interview?
     *
     * @param User $user
     *
     * @return bool
     */
    public function isInterviewer(User $user = null)
    {
        return $user && $user->getId() == $this->getInterviewer()->getId();
    }

    /**
     * Set scheduled.
     *
     * @param DateTime $scheduled
     *
     * @return Interview
     */
    public function setScheduled($scheduled)
    {
        $this->scheduled = $scheduled;
        $this->lastScheduleChanged = new DateTime();

        return $this;
    }

    /**
     * Get scheduled.
     *
     * @return DateTime
     */
    public function getScheduled()
    {
        return $this->scheduled;
    }

    /**
     * Set conducted.
     *
     * @param DateTime $conducted
     *
     * @return Interview
     */
    public function setConducted($conducted)
    {
        $this->conducted = $conducted;

        return $this;
    }

    /**
     * Get conducted.
     *
     * @return DateTime
     */
    public function getConducted()
    {
        return $this->conducted;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    public function isDraft()
    {
        return !$this->interviewed && $this->interviewScore !== null;
    }

    /**
     * @return Application
     */
    public function getApplication()
    {
        return $this->application;
    }

    /**
     * @return string
     */
    public function getInterviewStatusAsString(): string
    {
        switch ($this->interviewStatus) {
            case InterviewStatusType::NO_CONTACT:
                return 'Ikke satt opp';
            case InterviewStatusType::PENDING:
                return 'Ingen svar';
            case InterviewStatusType::ACCEPTED:
                return 'Akseptert';
            case InterviewStatusType::REQUEST_NEW_TIME:
                return 'Ny tid ønskes';
            case InterviewStatusType::CANCELLED:
                return 'Kansellert';
            default:
                return 'Ingen svar';
        }
    }

    /**
     * @return string
     */
    public function getInterviewStatusAsColor(): string
    {
        switch ($this->interviewStatus) {
            case InterviewStatusType::NO_CONTACT:
                return '#9999ff';
            case InterviewStatusType::PENDING:
                return '#0d97c4';
            case InterviewStatusType::ACCEPTED:
                return '#32CD32';
            case InterviewStatusType::REQUEST_NEW_TIME:
                return '#F08A24';
            case InterviewStatusType::CANCELLED:
                return '#f40f0f';
            default:
                return '#000000';
        }
    }

    /**
     * @return bool
     */
    public function isPending(): bool
    {
        return $this->interviewStatus === InterviewStatusType::PENDING;
    }

    /**
     * @param int $interviewStatus
     */
    public function setInterviewStatus(int $interviewStatus)
    {
        $this->interviewStatus = $interviewStatus;
    }

    public function acceptInterview()
    {
        $this->setInterviewStatus(InterviewStatusType::ACCEPTED);
    }

    public function requestNewTime()
    {
        $this->setInterviewStatus(InterviewStatusType::REQUEST_NEW_TIME);
    }

    public function cancel()
    {
        $this->setInterviewStatus(InterviewStatusType::CANCELLED);
    }

    public function resetStatus()
    {
        $this->setInterviewStatus(InterviewStatusType::PENDING);
    }

    /**
     * @return bool
     */
    public function isCancelled()
    {
        return $this->interviewStatus === InterviewStatusType::CANCELLED;
    }

    /**
     * @return string
     */
    public function getResponseCode()
    {
        return $this->responseCode;
    }

    public function setResponseCode(string $responseCode)
    {
        $this->responseCode = $responseCode;
    }

    /**
     * @return string
     */
    public function generateAndSetResponseCode()
    {
        $newResponseCode = bin2hex(openssl_random_pseudo_bytes(12));
        $this->responseCode = $newResponseCode;

        return $newResponseCode;
    }

    /**
     * @return string
     */
    public function getCancelMessage(): string
    {
        if ($this->cancelMessage !== null) {
            return $this->cancelMessage;
        } else {
            return '';
        }
    }

    /**
     * @param string $cancelMessage
     */
    public function setCancelMessage(string $cancelMessage = null)
    {
        $this->cancelMessage = $cancelMessage;
    }

    /**
     * @param int $newStatus
     */
    public function setStatus(int $newStatus)
    {
        if ($newStatus >= 0 && $newStatus <= 4) {
            $this->interviewStatus = $newStatus;
        } else {
            throw new InvalidArgumentException('Invalid status');
        }
    }

    /**
     * @return string
     */
    public function getNewTimeMessage(): string
    {
        return $this->newTimeMessage;
    }

    /**
     * @param string $newTimeMessage
     */
    public function setNewTimeMessage($newTimeMessage)
    {
        $this->newTimeMessage = $newTimeMessage;
    }

    /**
     * @return int
     */
    public function getInterviewStatus(): int
    {
        return $this->interviewStatus;
    }

    /**
     * @return DateTime
     */
    public function getLastScheduleChanged()
    {
        return $this->lastScheduleChanged;
    }

    /**
     * @return int
     */
    public function getNumAcceptInterviewRemindersSent(): ?int
    {
        return $this->numAcceptInterviewRemindersSent;
    }

    /**
     * @param int $numAcceptInterviewRemindersSent
     *
     * @return Interview
     */
    public function setNumAcceptInterviewRemindersSent(int $numAcceptInterviewRemindersSent): Interview
    {
        $this->numAcceptInterviewRemindersSent = $numAcceptInterviewRemindersSent;
        return $this;
    }

    /**
     * Increments number of accept-interview reminders sent
     */
    public function incrementNumAcceptInterviewRemindersSent()
    {
        $this->numAcceptInterviewRemindersSent++;
    }
}
