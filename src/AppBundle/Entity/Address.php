<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Address
 * @package AppBundle\Entity
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\AddressRepository")
 */
class Address
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;


    /**
     * @ORM\OneToOne(targetEntity="User", inversedBy="address")
     * @ORM\JoinColumn(onDelete="SET NULL")
     * @Assert\Valid
     * @Assert\NotNull(message="Dette feltet kan ikke være tomt")
     **/
    protected $user;


    /**
     * @ORM\Column(type="string")
     * @Assert\Length(max=255)
     * @Assert\NotBlank(message="Dette feltet kan ikke være tomt")
     */
    protected $address;


    /**
     * @ORM\Column(type="string")
     * @Assert\Length(max=255)
     * @Assert\NotBlank(message="Dette feltet kan ikke være tomt")
     */
    protected $city;


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
    public function getAddress()
    {
        return $this->address;
    }


    /**
     * @param string $address
     */
    public function setAddress($address): void
    {
        $this->address = $address;
    }


    /**
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }


    /**
     * @param string $city
     */
    public function setCity($city): void
    {
        $this->city = $city;
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
    public function setUser($user): void
    {
        $this->user = $user;
    }

}
