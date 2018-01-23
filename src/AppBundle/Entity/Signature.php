<?php
/**
 * Created by PhpStorm.
 * User: ArntErik
 * Date: 18.09.2016
 * Time: 17.13.
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="signature")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\SignatureRepository")
 */
class Signature
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="signature_path", type="string", length=45, nullable=true)
     */
    private $signaturePath;

    /**
     * @ORM\Column(type="string", length=250)
     * @Assert\NotBlank(message="Dette feltet kan ikke være tomt.")
     * @Assert\Length(min = 1, max = 250, maxMessage="Beskrivelsen kan maks være 250 tegn."))
     */
    private $description;

    /**
     * @ORM\OneToOne(targetEntity="User", cascade={"persist"})
     */
    protected $user;

    /**
     * @return string
     */
    public function getSignaturePath()
    {
        return $this->signaturePath;
    }

    /**
     * @param string $signaturePath
     */
    public function setSignaturePath($signaturePath)
    {
        $this->signaturePath = $signaturePath;
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
}
