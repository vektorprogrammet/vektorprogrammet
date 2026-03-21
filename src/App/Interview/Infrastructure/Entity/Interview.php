<?php

namespace App\Interview\Infrastructure\Entity;

use App\Admission\Infrastructure\Entity\Application;
use App\Identity\Infrastructure\Entity\User;
use App\Interview\Domain\ValueObjects\InterviewStatusType;
use App\Interview\Infrastructure\Repository\InterviewRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: InterviewRepository::class)]
#[ORM\Table(name: 'interview')]
class Interview
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private $id;

    #[ORM\Column(type: 'boolean')]
    private $interviewed;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private $scheduled;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private $lastScheduleChanged;

    #[ORM\Column(type: 'string', nullable: true)]
    private $room;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Assert\Length(max: 255, maxMessage: 'Campusnavn kan ikke være mer enn 255 tegn')]
    private $campus;

    #[ORM\Column(type: 'string', length: 500, nullable: true)]
    #[Assert\Length(max: 500, maxMessage: 'Linken kan ikke være mer enn 500 tegn')]
    private $mapLink;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private $conducted;

    #[ORM\ManyToOne(targetEntity: InterviewSchema::class)]
    #[ORM\JoinColumn(name: 'schema_id', referencedColumnName: 'id')]
    private $interviewSchema; // Bidirectional, may turn out to be unidirectional

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'interviews')]
    #[ORM\JoinColumn(name: 'interviewer_id', referencedColumnName: 'id', onDelete: 'SET NULL')]
    private $interviewer; // Unidirectional, may turn out to be bidirectional

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    private $coInterviewer;

    #[ORM\OneToMany(targetEntity: InterviewAnswer::class, mappedBy: 'interview', cascade: ['persist', 'remove'])]
    #[Assert\Valid]
    private $interviewAnswers;

    /**
     * @var InterviewScore
     */
    #[ORM\OneToOne(targetEntity: InterviewScore::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(name: 'interview_score_id', referencedColumnName: 'id')]
    #[Assert\Valid]
    private $interviewScore;

    /**
     * @var int
     */
    #[ORM\Column(type: 'integer', nullable: false)]
    private $interviewStatus;

    #[ORM\ManyToOne(targetEntity: User::class, cascade: ['persist'])]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private $user;

    #[ORM\OneToOne(targetEntity: Application::class, mappedBy: 'interview')]
    /** @phpstan-ignore property.onlyRead (Doctrine manages this inverse side via mappedBy) */
    private $application;

    /**
     * @var string
     */
    #[ORM\Column(type: 'string', nullable: true)]
    private $responseCode;

    /**
     * @var string
     */
    #[ORM\Column(type: 'string', nullable: true, length: 2000)]
    #[Assert\Length(max: 2000)]
    private $cancelMessage;

    /**
     * @var string
     */
    #[ORM\Column(type: 'string', length: 2000)]
    #[Assert\Length(max: 2000)]
    #[Assert\NotBlank(groups: ['newTimeRequest'], message: 'Meldingsboksen kan ikke være tom')]
    private $newTimeMessage;

    /**
     * @var int
     */
    #[ORM\Column(type: 'integer', options: ['default' => 0])]
    private $numAcceptInterviewRemindersSent;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->interviewAnswers = new ArrayCollection();
        $this->conducted = null;
        $this->interviewed = false;
        $this->interviewStatus = InterviewStatusType::NO_CONTACT;
        $this->newTimeMessage = '';
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
     * @return Interview
     */
    public function setInterviewSchema(?InterviewSchema $interviewSchema = null)
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
     * @return User|null
     */
    public function getCoInterviewer()
    {
        return $this->coInterviewer;
    }

    /**
     * Is the given User the co-interviewer of this Interview?
     *
     * @return bool
     */
    public function isCoInterviewer(?User $user = null)
    {
        return $user !== null && $this->getCoInterviewer() !== null && $user->getId() === $this->getCoInterviewer()->getId();
    }

    /**
     * Set interviewer.
     *
     * @return Interview
     */
    public function setCoInterviewer(?User $coInterviewer = null)
    {
        $this->coInterviewer = $coInterviewer;

        return $this;
    }

    /**
     * Set interviewer.
     *
     * @return Interview
     */
    public function setInterviewer(?User $interviewer = null)
    {
        $this->interviewer = $interviewer;

        return $this;
    }

    /**
     * Get interviewer.
     *
     * @return User|null
     */
    public function getInterviewer()
    {
        return $this->interviewer;
    }

    /**
     * Add interviewAnswers.
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
     */
    public function removeInterviewAnswer(InterviewAnswer $interviewAnswers)
    {
        $this->interviewAnswers->removeElement($interviewAnswers);
    }

    /**
     * Get interviewAnswers.
     *
     * @return Collection
     */
    public function getInterviewAnswers()
    {
        return $this->interviewAnswers;
    }

    /**
     * Set interviewScore.
     *
     * @return Interview
     */
    public function setInterviewScore(?InterviewScore $interviewScore = null)
    {
        $this->interviewScore = $interviewScore;

        return $this;
    }

    /**
     * Get interviewScore.
     *
     * @return InterviewScore|null
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
        }
        // setCancelled(false) is a no-op: clearing the cancelled flag does not
        // imply acceptance. Use acceptInterview() explicitly when needed.
    }

    /**
     * @return string|null
     */
    public function getRoom()
    {
        return $this->room;
    }

    /**
     * @param string|null $room
     */
    public function setRoom($room)
    {
        $this->room = $room;
    }

    /**
     * @return string|null
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
     * @return string|null
     */
    public function getMapLink()
    {
        return $this->mapLink;
    }

    /**
     * @param string|null $mapLink
     */
    public function setMapLink($mapLink)
    {
        $this->mapLink = $mapLink;
    }

    /**
     * Is the given User the interviewer of this Interview?
     *
     * @return bool
     */
    public function isInterviewer(?User $user = null)
    {
        return $user !== null && $this->getInterviewer() !== null && $user->getId() === $this->getInterviewer()->getId();
    }

    /**
     * Set scheduled.
     *
     * @param \DateTime $scheduled
     *
     * @return Interview
     */
    public function setScheduled($scheduled)
    {
        $this->scheduled = $scheduled;
        $this->lastScheduleChanged = new \DateTime();

        return $this;
    }

    /**
     * Get scheduled.
     *
     * @return \DateTime|null
     */
    public function getScheduled()
    {
        return $this->scheduled;
    }

    /**
     * Set conducted.
     *
     * @param \DateTime $conducted
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
     * @return \DateTime
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

    public function getInterviewStatusAsString(): string
    {
        $status = match ($this->interviewStatus) {
            InterviewStatusType::NO_CONTACT => 'Ikke satt opp',
            InterviewStatusType::PENDING => 'Ingen svar',
            InterviewStatusType::ACCEPTED => 'Akseptert',
            InterviewStatusType::REQUEST_NEW_TIME => 'Ny tid ønskes',
            InterviewStatusType::CANCELLED => 'Kansellert',
            default => 'Ingen svar',
        };

        return $status;
    }

    public function getInterviewStatusAsColor(): string
    {
        $color = match ($this->interviewStatus) {
            InterviewStatusType::NO_CONTACT => '#9999ff',
            InterviewStatusType::PENDING => '#0d97c4',
            InterviewStatusType::ACCEPTED => '#32CD32',
            InterviewStatusType::REQUEST_NEW_TIME => '#F08A24',
            InterviewStatusType::CANCELLED => '#f40f0f',
            default => '#000000',
        };

        return $color;
    }

    public function isPending(): bool
    {
        return $this->interviewStatus === InterviewStatusType::PENDING;
    }

    public function setInterviewStatus(int $interviewStatus): void
    {
        $validTransitions = [
            InterviewStatusType::NO_CONTACT => [InterviewStatusType::PENDING, InterviewStatusType::ACCEPTED, InterviewStatusType::CANCELLED],
            InterviewStatusType::PENDING => [InterviewStatusType::ACCEPTED, InterviewStatusType::REQUEST_NEW_TIME, InterviewStatusType::CANCELLED],
            InterviewStatusType::ACCEPTED => [InterviewStatusType::CANCELLED],
            InterviewStatusType::REQUEST_NEW_TIME => [InterviewStatusType::PENDING, InterviewStatusType::CANCELLED],
            InterviewStatusType::CANCELLED => [],
        ];

        $currentStatus = $this->interviewStatus;
        if ($currentStatus !== null && $currentStatus !== $interviewStatus
            && array_key_exists($currentStatus, $validTransitions)
            && !in_array($interviewStatus, $validTransitions[$currentStatus], true)) {
            throw new \InvalidArgumentException("Invalid interview status transition from $currentStatus to $interviewStatus");
        }

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
     * @return string|null
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

    public function getCancelMessage(): string
    {
        if ($this->cancelMessage !== null) {
            return $this->cancelMessage;
        }

        return '';
    }

    public function setCancelMessage(?string $cancelMessage = null)
    {
        $this->cancelMessage = $cancelMessage;
    }

    public function setStatus(int $newStatus): void
    {
        if ($newStatus < 0 || $newStatus > 4) {
            throw new \InvalidArgumentException('Invalid status');
        }
        $this->setInterviewStatus($newStatus);
    }

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

    public function getInterviewStatus(): int
    {
        return $this->interviewStatus;
    }

    /**
     * @return \DateTime
     */
    public function getLastScheduleChanged()
    {
        return $this->lastScheduleChanged;
    }

    public function getNumAcceptInterviewRemindersSent(): ?int
    {
        return $this->numAcceptInterviewRemindersSent;
    }

    public function setNumAcceptInterviewRemindersSent(int $numAcceptInterviewRemindersSent): Interview
    {
        $this->numAcceptInterviewRemindersSent = $numAcceptInterviewRemindersSent;

        return $this;
    }

    /**
     * Increments number of accept-interview reminders sent.
     */
    public function incrementNumAcceptInterviewRemindersSent()
    {
        ++$this->numAcceptInterviewRemindersSent;
    }
}
