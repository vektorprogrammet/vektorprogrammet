<?php
/**
 * Created by PhpStorm.
 * User: ArntErik
 * Date: 18.09.2016
 * Time: 17.13
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

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
     * @ORM\Column(type="string", length=45, nullable=true)
     */
    private $signature_path;

    /**
     * @ORM\Column(type="string", length=45)
     */
    private $description;

    /**
     * @ORM\OneToOne(targetEntity="User", cascade={"persist"})
     */
    protected $user;

    /**
     * @return mixed
     */
    public function getSignaturePath()
    {
        return $this->signature_path;
    }

    /**
     * @param mixed $signature_path
     */
    public function setSignaturePath($signature_path)
    {
        $this->signature_path = $signature_path;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }




}