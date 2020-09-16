<?php

namespace AppBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\ReceiptRepository")
 * @ORM\Table(name="receipt")
 */
class Receipt
{
    const STATUS_PENDING = 'pending';
    const STATUS_REFUNDED = 'refunded';
    const STATUS_REJECTED = 'rejected';
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="receipts")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $user;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $submitDate;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\NotBlank(message="Dette feltet kan ikke være tomt.")
     */
    private $receiptDate;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $refundDate;

    /**
     * @ORM\Column(name="picture_path", type="string", nullable=true)
     */
    private $picturePath;

    /**
     * @ORM\Column(type="string", length=5000)
     * @Assert\NotBlank(message="Dette feltet kan ikke være tomt.")
     * @Assert\Length(max="5000", maxMessage="Maks 5000 tegn")
     */
    private $description;

    /**
     * @ORM\Column(type="float")
     * @Assert\NotBlank(message="Dette feltet kan ikke være tomt.")
     * @Assert\GreaterThan(0, message="Ugyldig sum")
     */
    private $sum;

    /**
     * @ORM\Column(type="string")
     */
    private $status;

    /**
     * @ORM\Column(name="visual_id", type="string", nullable=true)
     */
    private $visualId;

    public function __construct()
    {
        $this->status = self::STATUS_PENDING;
        $this->submitDate = new DateTime();
        $this->receiptDate = new DateTime();
        $currentTimeInMilliseconds = round(microtime(true) * 1000);
        $this->visualId = dechex($currentTimeInMilliseconds);
    }

    /**
     * @return \AppBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param \AppBundle\Entity\User $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return DateTime
     */
    public function getSubmitDate()
    {
        return $this->submitDate;
    }

    /**
     * @param DateTime $submitDate
     */
    public function setSubmitDate($submitDate)
    {
        $this->submitDate = $submitDate;
    }


    /**
     * @return DateTime
     */
    public function getReceiptDate()
    {
        return $this->receiptDate;
    }

    /**
     * @param DateTime $receiptDate
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
     * @param string $picturePath
     */
    public function setPicturePath($picturePath)
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

    /**
     * @return string
     */
    public function getVisualId(): string
    {
        return $this->visualId;
    }

    /**
     * @param string $visualId
     */
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
    public function setStatus($status)
    {
        $this->status = $status;
    }

    public function __toString()
    {
        return $this->visualId;
    }

    /**
     * @return DateTime
     */
    public function getRefundDate()
    {
        return $this->refundDate;
    }

    /**
     * @param DateTime $refundDate
     */
    public function setRefundDate($refundDate)
    {
        $this->refundDate = $refundDate;
    }
}
