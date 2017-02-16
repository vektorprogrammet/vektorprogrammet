<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="newsletter")
 * @ORM\Entity()
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\NewsletterRepository")
 */
class Newsletter
{
    /**
     * @ORM\Column(name="id", type="integer", length=11)
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="name", type="string")
     */
    private $name;

    /**
     * @var Department
     *
     * @ORM\ManyToOne(targetEntity="Department")
     */
    private $department;

    /**
     * @var Letter
     *
     * @ORM\OneToMany(targetEntity="Letter", mappedBy="newsletter", cascade={"remove"})
     * @ORM\JoinColumn(referencedColumnName="id")
     */
    private $letters;

    /**
     * @var Subscriber[]
     *
     * @ORM\OneToMany(targetEntity="Subscriber", mappedBy="newsletter", cascade={"remove"})
     * @ORM\JoinColumn(referencedColumnName="id")
     */
    private $subscribers;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean")
     */
    private $showOnAdmissionPage;

    public function __construct()
    {
        $this->subscribers = new ArrayCollection();
        $this->letters = new ArrayCollection();
        $this->showOnAdmissionPage = false;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return Department
     */
    public function getDepartment()
    {
        return $this->department;
    }

    /**
     * @param Department $department
     */
    public function setDepartment($department)
    {
        $this->department = $department;
    }

    /**
     * @return Letter
     */
    public function getLetters()
    {
        return $this->letters;
    }

    /**
     * @param Letter $letters
     */
    public function setLetters($letters)
    {
        $this->letters = $letters;
    }

    /**
     * @param Letter $letter
     */
    public function addLetter(Letter $letter)
    {
        $this->letters[] = $letter;
    }

    /**
     * @return Subscriber[]
     */
    public function getSubscribers()
    {
        return $this->subscribers;
    }

    /**
     * @param Subscriber $subscribers
     */
    public function setSubscribers($subscribers)
    {
        $this->subscribers = $subscribers;
    }

    /**
     * @param Subscriber $subscriber
     */
    public function addSubscriber(Subscriber $subscriber)
    {
        $this->subscribers[] = $subscriber;
    }

    /**
     * @return bool
     */
    public function isShowOnAdmissionPage()
    {
        return $this->showOnAdmissionPage;
    }

    /**
     * @param bool $showOnAdmissionPage
     */
    public function setShowOnAdmissionPage($showOnAdmissionPage)
    {
        $this->showOnAdmissionPage = $showOnAdmissionPage;
    }
}
