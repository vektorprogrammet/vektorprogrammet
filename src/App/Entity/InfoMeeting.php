<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\Entity\Repository\InfoMeetingRepository;
use App\Validator\Constraints as CustomAssert;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name: 'infomeeting')]
#[ORM\Entity(repositoryClass: InfoMeetingRepository::class)]
#[CustomAssert\InfoMeeting]
#[ApiResource(
    operations: [
        new GetCollection(),
        new Get(),
    ],
    normalizationContext: ['groups' => ['info_meeting:read']],
)]
class InfoMeeting implements \Stringable
{
    #[ORM\Column(type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[Groups(['info_meeting:read', 'admission:read', 'department:detail'])]
    private $id;

    /**
     * @var bool
     */
    #[ORM\Column(type: 'boolean', nullable: true)]
    #[Groups(['info_meeting:read', 'admission:read', 'department:detail'])]
    private $showOnPage;

    #[ORM\Column(type: 'datetime', length: 250, nullable: true)]
    #[Assert\DateTime]
    #[Groups(['info_meeting:read', 'admission:read', 'department:detail'])]
    private $date;

    #[ORM\Column(type: 'string', length: 250, nullable: true)]
    #[Assert\Length(max: 250)]
    #[Groups(['info_meeting:read', 'admission:read', 'department:detail'])]
    private $room;

    #[ORM\Column(type: 'string', length: 250, nullable: true)]
    #[Assert\Length(max: 250)]
    #[Groups(['info_meeting:read', 'admission:read', 'department:detail'])]
    private $description;

    #[ORM\Column(type: 'string', length: 250, nullable: true)]
    #[Assert\Length(max: 250)]
    #[Groups(['info_meeting:read', 'admission:read', 'department:detail'])]
    private $link;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param \DateTime $date
     */
    public function setDate($date)
    {
        $this->date = $date;
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

    public function __toString(): string
    {
        return 'Infomøte';
    }

    /**
     * @return bool
     */
    public function isShowOnPage()
    {
        return $this->showOnPage;
    }

    /**
     * @param bool $showOnPage
     */
    public function setShowOnPage($showOnPage)
    {
        $this->showOnPage = $showOnPage;
    }

    /**
     * @return string
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * @param string $link
     */
    public function setLink($link)
    {
        if (strlen($link) > 0 && !str_starts_with($link, 'http')) {
            $link = "http://$link";
        }

        $this->link = $link;
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
}
