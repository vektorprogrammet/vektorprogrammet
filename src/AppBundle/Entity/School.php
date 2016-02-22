<?php


namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\SchoolRepository")
 * @ORM\Table(name="school")
 */
class School
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     */
    protected $name;

    /**
    * @ORM\Column(type="string")
    */
    protected $contactPerson;

    /**
     * @ORM\ManyToMany(targetEntity="Department", mappedBy="schools")
	 * @ORM\JoinColumn(onDelete="cascade")
     **/
    protected $departments;


    /**
     * @ORM\Column(type="string")
     */
    protected $email;
	
	/**
     * @ORM\ManyToMany(targetEntity="Subforum", mappedBy="schools")
	 * @ORM\JoinColumn(onDelete="cascade")
     **/
	protected $subforums;
	
    /**
     * @ORM\Column(type="string")
     */
    protected $phone;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    private $international;

    public function __construct() {
        $this->departments = new \Doctrine\Common\Collections\ArrayCollection();
		$this->subforums = new \Doctrine\Common\Collections\ArrayCollection();
        $this->international = false;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return School
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set contactPerson
     *
     * @param string $contactPerson
     * @return School
     */
    public function setContactPerson($contactPerson)
    {
        $this->contactPerson = $contactPerson;

        return $this;
    }

    /**
     * Get contactPerson
     *
     * @return string
     */
    public function getContactPerson()
    {
        return $this->contactPerson;
    }

    /**
     * Add departments
     *
     * @param \AppBundle\Entity\Department $departments
     * @return School
     */
    public function addDepartment(\AppBundle\Entity\Department $departments)
    {
        $this->departments[] = $departments;

        return $this;
    }

    /**
     * Remove departments
     *
     * @param \AppBundle\Entity\Department $departments
     */
    public function removeDepartment(\AppBundle\Entity\Department $departments)
    {
        $this->departments->removeElement($departments);
    }

    /**
     * Get departments
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDepartments()
    {
        return $this->departments;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return School
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set phone
     *
     * @param string $phone
     * @return School
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }
	
	public function __toString(){
		return $this->getName();
	}
	

    /**
     * Add subforums
     *
     * @param \AppBundle\Entity\Subforum $subforums
     * @return School
     */
    public function addSubforum(\AppBundle\Entity\Subforum $subforums)
    {
        $this->subforums[] = $subforums;

        return $this;
    }

    /**
     * Remove subforums
     *
     * @param \AppBundle\Entity\Subforum $subforums
     */
    public function removeSubforum(\AppBundle\Entity\Subforum $subforums)
    {
        $this->subforums->removeElement($subforums);
    }

    /**
     * Get subforums
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSubforums()
    {
        return $this->subforums;
    }
	
	// Used for unit testing 
	public function fromArray($data = array())
    {
        foreach ($data as $property => $value) {
            $method = "set{$property}";
            $this->$method($value);
        }
    }

    /**
     * @return boolean
     */
    public function isInternational()
    {
        return $this->international;
    }

    /**
     * @param boolean $international
     */
    public function setInternational($international)
    {
        $this->international = $international;
    }


}
