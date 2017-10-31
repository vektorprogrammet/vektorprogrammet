<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Table(name="field_of_study")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\FieldOfStudyRepository")
 * @JMS\ExclusionPolicy("all")
 */
class FieldOfStudy
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @JMS\Expose()
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=250)
     * @JMS\Expose()
     */
    private $name;

    /**
     * @ORM\Column(name="short_name", type="string", length=50)
     * @JMS\Expose()
     */
    private $shortName;

    /**
     * @ORM\ManyToOne(targetEntity="Department", inversedBy="fieldOfStudy")
     */
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
     * @param \AppBundle\Entity\Department $department
     *
     * @return FieldOfStudy
     */
    public function setDepartment(\AppBundle\Entity\Department $department = null)
    {
        $this->department = $department;

        return $this;
    }

    /**
     * Get department.
     *
     * @return \AppBundle\Entity\Department
     */
    public function getDepartment()
    {
        return $this->department;
    }

    public function __toString()
    {
        return $this->getShortName();
    }
}
