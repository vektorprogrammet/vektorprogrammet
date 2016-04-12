<?php

namespace AppBundle\Entity;

use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * AppBundle\Entity\User
 *
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\UserRepository")
 * @UniqueEntity(
 *      fields = {"email"},
 *      message = "Denne Eposten er allerede i bruk.")
 *
 * @UniqueEntity(
 *      fields = {"user_name"},
 *      message = "Dette brukernavnet er allerede i bruk.")
 */
class User implements AdvancedUserInterface, \Serializable {

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
	
	/**
     * @ORM\Column(type="string", length=45)
     * @Assert\NotBlank(groups={"admission", "create_user"}, message="Dette feltet kan ikke være tomt.")
     */
    private $lastName;
	
	/**
     * @ORM\Column(type="string", length=45)
     * @Assert\NotBlank(groups={"admission", "create_user"}, message="Dette feltet kan ikke være tomt.")
     */
    private $firstName;
	
	/**
	 * @ORM\ManyToOne(targetEntity="FieldOfStudy")
	 * @ORM\JoinColumn(onDelete="SET NULL")
     * @Assert\Valid
     */
    private $fieldOfStudy;
	
	/**
     * @ORM\Column(name="gender", type="boolean")
     * @Assert\NotBlank(groups={"admission", "create_user"}, message="Dette feltet kan ikke være tomt.")
     */
    private $gender;
	
	/**
     * @ORM\Column(type="string", length=45)
     */
    private $picture_path;
	
	/**
     * @ORM\Column(type="string", length=45)
     * @Assert\NotBlank(groups={"admission", "create_user"}, message="Dette feltet kan ikke være tomt.")
     */
    private $phone;

    /**
     * @ORM\Column(type="string", length=45, unique=true, nullable=true)
     * @Assert\NotBlank(groups={"create_user"}, message="Dette feltet kan ikke være tomt.")
     * @Assert\Unique(groups={"create_user"}, message="Brukernavnet er allerede i bruk.")

     */
    private $user_name;

    /**
     * @ORM\Column(type="string", length=64, nullable=true)
     * @Assert\NotBlank(groups={"create_user"}, message="Dette feltet kan ikke være tomt.")
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=45, unique=true)
     * @Assert\NotBlank(groups={"admission", "create_user"}, message="Dette feltet kan ikke være tomt.")
     * @Assert\Email(groups={"admission", "create_user"}, message="Ikke gyldig e-post.")
     * @Assert\Unique(groups={"create_user"}, message="E-posten er allerede i bruk.")
     */
    private $email;

    /**
     * @ORM\Column(name="is_active", type="boolean")
     */
    private $isActive;
	
	/**
     * @ORM\ManyToMany(targetEntity="Role", inversedBy="users")
     * @ORM\JoinColumn(onDelete="cascade")
     * @Assert\Valid
     */
    private $roles;

    /**
     * @ORM\column(type="string", nullable=true)
     */
	private $new_user_code;

    /**
     * @ORM\OneToMany(targetEntity="AssistantHistory", mappedBy="user")
     */
    private $assistantHistories;

	/**
     * @ORM\OneToMany(targetEntity="CertificateRequest", mappedBy="user")
     **/
	protected $certificateRequests;


	
	public function __construct() {
        $this->roles = new ArrayCollection();
		$this->fieldOfStudy = new ArrayCollection();
		$this->certificateRequests = new ArrayCollection();
		$this->isActive = false;
        $this->picture_path = 'images/defaultProfile.png';
    }
	

    function getId() {
        return $this->id;
    }
	
	public function getGender() {
        return $this->gender;
    }
	
	public function getFirstName() {
        return $this->firstName;
    }

	public function getLastName() {
        return $this->lastName;
    }

    /**
     * @return string
     */
    public function getFullName(){
        return $this->getFirstName() . " " . $this->getLastName();
    }

    function getEmail() {
        return $this->email;
    }

    function getIsActive() {
        return $this->isActive;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setPassword($password) {
        $this->password = password_hash($password, PASSWORD_BCRYPT, array('cost' => 12));
    }
	
	/**
     * @inheritDoc
     */
    public function getPassword() {
        return $this->password;
    }

    function setEmail($email) {
        $this->email = $email;
    }

    function setIsActive($isActive) {
        $this->isActive = $isActive;
    }

    function setRoles($roles) {
        $this->roles = $roles;
    }
	
    

    public function getRoles() {
        return $this->roles->toArray();
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     * @return User
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Set firstName
     *
     * @param string $firstName
     * @return User
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Set gender
     *
     * @param string $gender
     * @return User
     */
    public function setGender($gender)
    {
        $this->gender = $gender;

        return $this;
    }

    /**
     * Set picture_path
     *
     * @param string $picturePath
     * @return User
     */
    public function setPicturePath($picturePath)
    {
        $this->picture_path = $picturePath;

        return $this;
    }

    /**
     * Get picture_path
     *
     * @return string 
     */
    public function getPicturePath()
    {
        return $this->picture_path;
    }

    /**
     * Set phone
     *
     * @param string $phone
     * @return User
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

    /**
     * Set user_name
     *
     * @param string $userName
     * @return User
     */
    public function setUserName($userName)
    {
        $this->user_name = $userName;

        return $this;
    }

    /**
     * Get user_name
     *
     * @return string 
     */
    public function getUserName()
    {
        return $this->user_name;
    }

    /**
     * Set fieldOfStudy
     *
     * @param FieldOfStudy $fieldOfStudy
     * @return User
     */
    public function setFieldOfStudy(FieldOfStudy $fieldOfStudy = null)
    {
        $this->fieldOfStudy = $fieldOfStudy;

        return $this;
    }

    /**
     * Get fieldOfStudy
     *
     * @return FieldOfStudy
     */
    public function getFieldOfStudy()
    {
        return $this->fieldOfStudy;
    }

    /**
     * Add roles
     *
     * @param Role $roles
     * @return User
     */
    public function addRole(Role $roles)
    {
        $this->roles[] = $roles;

        return $this;
    }

    /**
     * Remove roles
     *
     * @param Role $roles
     */
    public function removeRole(Role $roles)
    {
        $this->roles->removeElement($roles);
    }

    /**
     * Set new_user_code
     *
     * @param string $newUserCode
     * @return User
     */
    public function setNewUserCode($newUserCode)
    {
        $this->new_user_code = $newUserCode;

        return $this;
    }

    /**
     * Get new_user_code
     *
     * @return string 
     */
    public function getNewUserCode()
    {
        return $this->new_user_code;
    }

    /**
     * @return array
     */
    public function getAssistantHistories()
    {
        return $this->assistantHistories;
    }

    /**
     * @param array $assistantHistories
     */
    public function setAssistantHistories($assistantHistories)
    {
        $this->assistantHistories = $assistantHistories;
    }

    public function addAssistantHistory(AssistantHistory $assistantHistory){
        $this->assistantHistories[] = $assistantHistory;
    }


	
	/**
     * Add certificateRequests
     *
     * @param CertificateRequest $certificateRequests
     * @return User
     */
    public function addCertificateRequest(CertificateRequest $certificateRequests)
    {
        $this->certificateRequests[] = $certificateRequests;

        return $this;
    }

    /**
     * Remove certificateRequests
     *
     * @param CertificateRequest $certificateRequests
     */
    public function removeCertificateRequest(CertificateRequest $certificateRequests)
    {
        $this->certificateRequests->removeElement($certificateRequests);
    }

    /**
     * Get certificateRequests
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCertificateRequests()
    {
        return $this->certificateRequests;
    }
	
	// Used for unit testing 
	public function fromArray($data = array())
    {
        foreach ($data as $property => $value) {
            $method = "set{$property}";
            $this->$method($value);
        }
    }
		
	// toString method used to display the user in twig files 
	public function __toString()
	{	
		$firstName = $this->getFirstName();
		$lastName = $this->getLastName();
		$email = $this->getEmail();
		return "$firstName $lastName";
	}

	
	/*
	
	You may or may not need the code below depending on the algorithm you chose to hash and salt passwords with.
	The methods below are taken from the login guide on Symfony.com, which can be found here:
	http://symfony.com/doc/current/cookbook/security/form_login_setup.html
	http://symfony.com/doc/current/cookbook/security/entity_provider.html
	
	
	*/
	
	/**
     * @inheritDoc
     */
    public function eraseCredentials() {
        
    }

    /**
     * @see \Serializable::serialize()
     */
    public function serialize() {
        return serialize(array(
            $this->id,
            $this->user_name,
            $this->password,
                // see section on salt below
                // $this->salt,
        ));
    }

    /**
     * @see \Serializable::unserialize(
     */
    public function unserialize($serialized) {
        list (
                $this->id,
                $this->user_name,
                $this->password,
                // see section on salt below
                // $this->salt
                ) = unserialize($serialized);
    }

    public function isAccountNonExpired() {
        return true;
    }

    public function isAccountNonLocked() {
        return true;
    }

    public function isCredentialsNonExpired() {
        return true;
    }

    public function isEnabled() {
        return $this->isActive;
    }
	
	/**
     * @inheritDoc
     */
    public function getSalt() {
        // you *may* need a real salt depending on your encoder
        // see section on salt below
        return null;
    }
	

    
}
