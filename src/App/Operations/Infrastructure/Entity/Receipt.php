<?php

namespace App\Operations\Infrastructure\Entity;

use App\Identity\Infrastructure\Entity\User;
use App\Operations\Infrastructure\Repository\ReceiptRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ReceiptRepository::class)]
#[ORM\Table(name: 'receipt')]
class Receipt implements \Stringable
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_REFUNDED = 'refunded';
    public const STATUS_REJECTED = 'rejected';
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private $id;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'receipts')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private $user;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private $submitDate;

    #[ORM\Column(type: 'datetime')]
    #[Assert\NotBlank(message: 'Dette feltet kan ikke være tomt.')]
    private $receiptDate;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private $refundDate;

    #[ORM\Column(name: 'picture_path', type: 'string', nullable: true)]
    private $picturePath;

    #[ORM\Column(type: 'string', length: 5000)]
    #[Assert\NotBlank(message: 'Dette feltet kan ikke være tomt.')]
    #[Assert\Length(max: 5000, maxMessage: 'Maks 5000 tegn')]
    private $description;

    #[ORM\Column(type: 'float')]
    #[Assert\NotBlank(message: 'Dette feltet kan ikke være tomt.')]
    #[Assert\GreaterThan(0, message: 'Ugyldig sum')]
    private $sum;

    #[ORM\Column(type: 'string')]
    private $status;

    #[ORM\Column(name: 'visual_id', type: 'string', nullable: true, unique: true)]
    private $visualId;

    public function __construct()
    {
        $this->status = self::STATUS_PENDING;
        $this->submitDate = new \DateTime();
        $this->receiptDate = new \DateTime();
        $currentTimeInMilliseconds = (int) round(microtime(true) * 1000);
        $this->visualId = dechex($currentTimeInMilliseconds);
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

    /**
     * @return \DateTime
     */
    public function getSubmitDate()
    {
        return $this->submitDate;
    }

    /**
     * @param \DateTime $submitDate
     */
    public function setSubmitDate($submitDate)
    {
        $this->submitDate = $submitDate;
    }

    /**
     * @return \DateTime
     */
    public function getReceiptDate()
    {
        return $this->receiptDate;
    }

    /**
     * @param \DateTime $receiptDate
     */
    public function setReceiptDate($receiptDate)
    {
        $this->receiptDate = $receiptDate;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getPicturePath()
    {
        return $this->picturePath;
    }

    /**
     * @param string|null $picturePath
     */
    public function setPicturePath(?string $picturePath): void
    {
        $this->picturePath = $picturePath;
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
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return float
     */
    public function getSum()
    {
        return $this->sum;
    }

    /**
     * @param float $sum
     */
    public function setSum($sum)
    {
        $this->sum = $sum;
    }

    public function getVisualId(): string
    {
        return $this->visualId;
    }

    public function setVisualId(string $visualId)
    {
        $this->visualId = $visualId;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param string $status
     */
    public function setStatus($status): void
    {
        $validTransitions = [
            self::STATUS_PENDING => [self::STATUS_REFUNDED, self::STATUS_REJECTED],
            self::STATUS_REJECTED => [self::STATUS_PENDING],
            self::STATUS_REFUNDED => [],
        ];

        if ($this->status !== null && $this->status !== $status
            && array_key_exists($this->status, $validTransitions)
            && !in_array($status, $validTransitions[$this->status], true)) {
            throw new \InvalidArgumentException("Invalid receipt status transition from {$this->status} to {$status}");
        }

        $this->status = $status;
    }

    public function __toString(): string
    {
        return $this->visualId;
    }

    /**
     * @return \DateTime|null
     */
    public function getRefundDate(): ?\DateTime
    {
        return $this->refundDate;
    }

    /**
     * @param \DateTime $refundDate
     */
    public function setRefundDate($refundDate)
    {
        $this->refundDate = $refundDate;
    }
}
