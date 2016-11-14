<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="forum")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\ForumRepository")
 */
class Forum
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=250)
     */
    protected $name;

    /**
     * @ORM\Column(type="string", length=250)
     */
    protected $description;

    /**
     * @ORM\Column(type="string")
     */
    protected $type;

    /**
     * @ORM\ManyToMany(targetEntity="Subforum", inversedBy="forums")
     * @ORM\JoinTable(name="forum_subforum")
     * @ORM\JoinColumn(onDelete="cascade")
     **/
    protected $subforums;

    public function __construct()
    {
        $this->subforums = new \Doctrine\Common\Collections\ArrayCollection();
    }

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
     * @return Forum
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
     * Set description.
     *
     * @param string $description
     *
     * @return Forum
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Add subforums.
     *
     * @param \AppBundle\Entity\Subforum $subforums
     *
     * @return Forum
     */
    public function addSubforum(\AppBundle\Entity\Subforum $subforums)
    {
        $this->subforums[] = $subforums;

        return $this;
    }

    /**
     * Remove subforums.
     *
     * @param \AppBundle\Entity\Subforum $subforums
     */
    public function removeSubforum(\AppBundle\Entity\Subforum $subforums)
    {
        $this->subforums->removeElement($subforums);
    }

    /**
     * Get subforums.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSubforums()
    {
        return $this->subforums;
    }

    public function __toString()
    {
        return $this->getName();
    }

    /**
     * Set type.
     *
     * @param string $type
     *
     * @return Forum
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    // Used for unit testing
    public function fromArray($data = array())
    {
        foreach ($data as $property => $value) {
            $method = "set{$property}";
            $this->$method($value);
        }
    }
}
