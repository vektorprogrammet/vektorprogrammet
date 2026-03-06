<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\Entity\Repository\FieldOfStudyRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Table(name: 'field_of_study')]
#[ORM\Entity(repositoryClass: FieldOfStudyRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(),
    ],
    normalizationContext: ['groups' => ['field_of_study:read']],
)]
class FieldOfStudy implements \Stringable
{
    #[ORM\Column(type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[Groups(['field_of_study:read', 'department:detail'])]
    private $id;

    #[ORM\Column(type: 'string', length: 250)]
    #[Groups(['field_of_study:read', 'department:detail'])]
    private $name;

    #[ORM\Column(name: 'short_name', type: 'string', length: 50)]
    #[Groups(['field_of_study:read', 'department:detail'])]
    private $shortName;

    #[ORM\ManyToOne(targetEntity: 'Department', inversedBy: 'fieldOfStudy')]
    #[Groups(['field_of_study:read'])]
    private $department;

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
     * Set name.
     *
     * @param string $name
     *
     * @return FieldOfStudy
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set shortName.
     *
     * @param string $shortName
     *
     * @return FieldOfStudy
     */
    public function setShortName($shortName)
    {
        $this->shortName = $shortName;

        return $this;
    }

    /**
     * Get shortName.
     *
     * @return string
     */
    public function getShortName()
    {
        return $this->shortName;
    }

    /**
     * Set department.
     *
     * @return FieldOfStudy
     */
    public function setDepartment(?Department $department = null)
    {
        $this->department = $department;

        return $this;
    }

    /**
     * Get department.
     *
     * @return Department
     */
    public function getDepartment()
    {
        return $this->department;
    }

    public function __toString(): string
    {
        return $this->getShortName();
    }
}
