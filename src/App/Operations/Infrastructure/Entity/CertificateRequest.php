<?php

namespace App\Operations\Infrastructure\Entity;

use App\Entity\User;
use App\Operations\Infrastructure\Repository\CertificateRequestRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'certificate_request')]
#[ORM\Entity(repositoryClass: CertificateRequestRepository::class)]
class CertificateRequest
{
    #[ORM\Column(type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    protected $id;
    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'certificateRequests')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    protected $user;

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
     * Set user.
     *
     * @return CertificateRequest
     */
    public function setUser(?User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user.
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    // Used for unit testing
    public function fromArray($data = [])
    {
        foreach ($data as $property => $value) {
            $method = "set{$property}";
            $this->$method($value);
        }
    }
}
