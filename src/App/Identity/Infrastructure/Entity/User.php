<?php

namespace App\Identity\Infrastructure\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use App\Identity\Infrastructure\Repository\UserRepository;
use App\Interview\Infrastructure\Entity\Interview;
use App\Operations\Infrastructure\Entity\AssistantHistory;
use App\Operations\Infrastructure\Entity\CertificateRequest;
use App\Operations\Infrastructure\Entity\Receipt;
use App\Organization\Infrastructure\Entity\Department;
use App\Organization\Infrastructure\Entity\ExecutiveBoardMembership;
use App\Organization\Infrastructure\Entity\FieldOfStudy;
use App\Organization\Infrastructure\Entity\TeamMembership;
use App\Identity\Domain\Roles;
use App\Identity\Api\State\PublicUserProfileProvider;
use App\Identity\Infrastructure\Validator as CustomAssert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * App\Identity\Infrastructure\Entity\User.
 */
#[ORM\Table(name: 'user')]
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'Denne Eposten er allerede i bruk.', groups: ['create_user', 'edit_user'])]
#[UniqueEntity(fields: ['user_name'], message: 'Dette brukernavnet er allerede i bruk.', groups: ['create_user', 'username', 'edit_user'])]
#[ApiResource(
    operations: [
        new Get(
            uriTemplate: '/users/{id}',
            provider: PublicUserProfileProvider::class,
            normalizationContext: ['groups' => ['user:public']],
        ),
    ],
)]
class User implements UserInterface, PasswordAuthenticatedUserInterface, EquatableInterface, \Stringable
{
    #[ORM\Column(type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[Groups(['team_member:read', 'team:detail', 'user:public'])]
    private $id;

    #[ORM\Column(type: 'string')]
    #[Assert\NotBlank(groups: ['admission', 'create_user', 'edit_user'], message: 'Dette feltet kan ikke være tomt.')]
    #[Groups(['team_member:read', 'team:detail', 'user:public'])]
    private $lastName;

    #[ORM\Column(type: 'string')]
    #[Assert\NotBlank(groups: ['admission', 'create_user', 'edit_user'], message: 'Dette feltet kan ikke være tomt.')]
    #[Groups(['team_member:read', 'team:detail', 'user:public'])]
    private $firstName;

    /**
     * @var FieldOfStudy
     */
    #[ORM\ManyToOne(targetEntity: FieldOfStudy::class)]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    #[Assert\NotBlank(groups: ['admission', 'edit_user', 'create_user'], message: 'Dette feltet kan ikke være tomt.')]
    #[Assert\Valid]
    private $fieldOfStudy;

    #[ORM\Column(name: 'gender', type: 'boolean')]
    #[Assert\NotBlank(groups: ['admission', 'create_user'], message: 'Dette feltet kan ikke være tomt.')]
    private $gender;

    #[ORM\Column(type: 'string')]
    #[Groups(['team_member:read', 'team:detail', 'user:public'])]
    private $picture_path;

    #[ORM\Column(type: 'string')]
    #[Assert\NotBlank(groups: ['admission', 'create_user', 'edit_user'], message: 'Dette feltet kan ikke være tomt.')]
    private $phone;

    #[ORM\Column(type: 'string', length: 45, nullable: true)]
    private $accountNumber;

    #[ORM\Column(type: 'string', unique: true, nullable: true)]
    #[Assert\NotBlank(groups: ['username', 'edit_user'], message: 'Dette feltet kan ikke være tomt.')]
    private $user_name;

    #[ORM\Column(type: 'string', length: 64, nullable: true)]
    #[Assert\NotBlank(groups: ['username', 'edit_user'], message: 'Dette feltet kan ikke være tomt.')]
    private $password;

    #[ORM\Column(type: 'string', unique: true)]
    #[Assert\NotBlank(groups: ['admission', 'create_user', 'edit_user'], message: 'Dette feltet kan ikke være tomt.')]
    #[Assert\Email(groups: ['admission', 'create_user', 'edit_user'], message: 'Ikke gyldig e-post.')]
    private $email;

    #[ORM\Column(type: 'string', unique: true, nullable: true)]
    #[Assert\Email]
    #[CustomAssert\UniqueCompanyEmail]
    #[CustomAssert\VektorEmail]
    private $companyEmail;

    #[ORM\Column(name: 'is_active', type: 'boolean')]
    private $isActive;

    #[ORM\Column(type: 'boolean', nullable: false)]
    private $reservedFromPopUp;

    #[ORM\Column(type: 'datetime', nullable: false)]
    private $lastPopUpTime;

    /**
     * @var Role[]
     */
    #[ORM\ManyToMany(targetEntity: Role::class, inversedBy: 'users')]
    #[ORM\JoinColumn(onDelete: 'cascade')]
    #[Assert\Valid]
    private $roles;

    #[ORM\Column(type: 'string', nullable: true)]
    private $new_user_code;

    /**
     * @var AssistantHistory[]
     */
    #[ORM\OneToMany(targetEntity: AssistantHistory::class, mappedBy: 'user')]
    private $assistantHistories;

    /**
     * @var TeamMembership[]
     */
    #[ORM\OneToMany(targetEntity: TeamMembership::class, mappedBy: 'user')]
    private $teamMemberships;

    /**
     * @var ExecutiveBoardMembership[]
     */
    #[ORM\OneToMany(targetEntity: ExecutiveBoardMembership::class, mappedBy: 'user')]
    private $executiveBoardMemberships;
    #[ORM\OneToMany(targetEntity: CertificateRequest::class, mappedBy: 'user')]
    protected $certificateRequests;

    #[ORM\OneToMany(targetEntity: Interview::class, mappedBy: 'interviewer')]
    private $interviews;

    #[ORM\OneToMany(targetEntity: Receipt::class, mappedBy: 'user')]
    private $receipts;

    public function __construct()
    {
        $this->roles = new ArrayCollection();
        $this->certificateRequests = new ArrayCollection();
        $this->interviews = new ArrayCollection();
        $this->isActive = true;
        $this->picture_path = 'images/defaultProfile.png';
        $this->receipts = new ArrayCollection();
        $this->reservedFromPopUp = false;
        $this->lastPopUpTime = new \DateTime('2000-01-01');
    }

    public function getId()
    {
        return $this->id;
    }

    public function getDepartment(): Department
    {
        return $this->getFieldOfStudy()->getDepartment();
    }

    public function getGender()
    {
        return $this->gender;
    }

    public function getFirstName()
    {
        return $this->firstName;
    }

    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @return string
     */
    public function getFullName()
    {
        return $this->getFirstName().' '.$this->getLastName();
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function isActive()
    {
        return $this->isActive;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setPassword($password)
    {
        $this->password = password_hash((string) $password, PASSWORD_BCRYPT, ['cost' => 12]);
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function setActive($isActive)
    {
        $this->isActive = $isActive;
    }

    public function setRoles($roles)
    {
        $this->roles = $roles;
    }

    /**
     * @return string[]
     */
    public function getRoles(): array
    {
        $roles = is_array($this->roles) ? $this->roles : $this->roles->toArray();

        return array_map(fn (Role $role) => $role->getRole(), $roles);
    }

    /**
     * @return Role[]
     */
    public function getRoleEntities(): array
    {
        if (is_array($this->roles)) {
            return $this->roles;
        }

        return $this->roles->toArray();
    }

    /**
     * Set lastName.
     *
     * @param string $lastName
     *
     * @return User
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Set firstName.
     *
     * @param string $firstName
     *
     * @return User
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Set gender.
     *
     * @param string $gender
     *
     * @return User
     */
    public function setGender($gender)
    {
        $this->gender = $gender;

        return $this;
    }

    /**
     * Set picture_path.
     *
     * @param string $picturePath
     *
     * @return User
     */
    public function setPicturePath($picturePath)
    {
        $this->picture_path = $picturePath;

        return $this;
    }

    /**
     * Get picture_path.
     *
     * @return string
     */
    #[Groups(['team_member:read', 'team:detail', 'user:public'])]
    public function getPicturePath()
    {
        return $this->picture_path;
    }

    /**
     * Set phone.
     *
     * @param string $phone
     *
     * @return User
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone.
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @return string
     */
    public function getAccountNumber()
    {
        return $this->accountNumber;
    }

    /**
     * @param string $accountNumber
     */
    public function setAccountNumber($accountNumber)
    {
        $this->accountNumber = $accountNumber;
    }

    /**
     * Set user_name.
     *
     * @param string $userName
     *
     * @return User
     */
    public function setUserName($userName)
    {
        $this->user_name = $userName;

        return $this;
    }

    /**
     * Get user_name.
     *
     * @return string
     */
    public function getUserName()
    {
        return $this->user_name;
    }

    /**
     * Set fieldOfStudy.
     *
     * @return User
     */
    public function setFieldOfStudy(?FieldOfStudy $fieldOfStudy = null)
    {
        $this->fieldOfStudy = $fieldOfStudy;

        return $this;
    }

    /**
     * Get fieldOfStudy.
     *
     * @return FieldOfStudy
     */
    public function getFieldOfStudy()
    {
        return $this->fieldOfStudy;
    }

    /**
     * Add roles.
     *
     * @return User
     */
    public function addRole(Role $roles)
    {
        $this->roles[] = $roles;

        return $this;
    }

    /**
     * Remove roles.
     */
    public function removeRole(Role $roles)
    {
        $this->roles->removeElement($roles);
    }

    /**
     * Set new_user_code.
     *
     * @param string $newUserCode
     *
     * @return User
     */
    public function setNewUserCode($newUserCode)
    {
        $this->new_user_code = $newUserCode;

        return $this;
    }

    /**
     * Get new_user_code.
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

    public function hasBeenAssistant(): bool
    {
        if ($this->assistantHistories === null) {
            return false;
        }

        return !empty($this->assistantHistories->toArray());
    }

    public function isActiveAssistant(): bool
    {
        foreach ($this->assistantHistories as $history) {
            if ($history->getSemester()->isActive()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param array $assistantHistories
     */
    public function setAssistantHistories($assistantHistories)
    {
        $this->assistantHistories = $assistantHistories;
    }

    public function addAssistantHistory(AssistantHistory $assistantHistory)
    {
        $this->assistantHistories[] = $assistantHistory;
    }

    /**
     * Add certificateRequests.
     *
     * @return User
     */
    public function addCertificateRequest(CertificateRequest $certificateRequests)
    {
        $this->certificateRequests[] = $certificateRequests;

        return $this;
    }

    /**
     * Remove certificateRequests.
     */
    public function removeCertificateRequest(CertificateRequest $certificateRequests)
    {
        $this->certificateRequests->removeElement($certificateRequests);
    }

    /**
     * Get certificateRequests.
     *
     * @return Collection
     */
    public function getCertificateRequests()
    {
        return $this->certificateRequests;
    }

    // Used for unit testing
    public function fromArray($data = [])
    {
        foreach ($data as $property => $value) {
            $method = "set{$property}";
            $this->$method($value);
        }
    }

    // toString method used to display the user in twig files
    public function __toString(): string
    {
        return "{$this->getFirstName()} {$this->getLastName()}";
    }

    /*

    You may or may not need the code below depending on the algorithm you chose to hash and salt passwords with.
    The methods below are taken from the login guide on Symfony.com, which can be found here:
    http://symfony.com/doc/current/cookbook/security/form_login_setup.html
    http://symfony.com/doc/current/cookbook/security/entity_provider.html

    */

    public function eraseCredentials(): void
    {
    }

    public function __serialize(): array
    {
        return [
            'id' => $this->id,
            'user_name' => $this->user_name,
            'password' => $this->password,
        ];
    }

    public function __unserialize(array $data): void
    {
        $this->id = $data['id'];
        $this->user_name = $data['user_name'];
        $this->password = $data['password'];
    }

    public function isAccountNonExpired()
    {
        return true;
    }

    public function isAccountNonLocked()
    {
        return true;
    }

    public function isCredentialsNonExpired()
    {
        return true;
    }

    public function isEnabled()
    {
        return $this->isActive;
    }

    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @return TeamMembership[]
     */
    public function getTeamMemberships()
    {
        return $this->teamMemberships;
    }

    /**
     * @return Interview[]
     */
    public function getInterviews()
    {
        return $this->interviews->toArray();
    }

    /**
     * @return ArrayCollection
     */
    public function getReceipts()
    {
        return $this->receipts;
    }

    /**
     * @param Receipt
     */
    public function addReceipt($receipt)
    {
        $this->receipts->add($receipt);
    }

    /**
     * @return bool
     */
    public function hasPendingReceipts()
    {
        $numberOfPendingReceipts = $this->getNumberOfPendingReceipts();

        return $numberOfPendingReceipts !== 0;
    }

    /**
     * @return int
     */
    public function getNumberOfPendingReceipts()
    {
        $num = 0;
        foreach ($this->receipts as $receipt) {
            if ($receipt->getStatus() === Receipt::STATUS_PENDING) {
                ++$num;
            }
        }

        return $num;
    }

    public function getTotalPendingReceiptSum(): float
    {
        $totalSum = 0.0;
        foreach ($this->receipts as $receipt) {
            if ($receipt->getStatus() === Receipt::STATUS_PENDING) {
                $totalSum += $receipt->getSum();
            }
        }

        return $totalSum;
    }

    public function getTotalRefundedReceiptSum(): float
    {
        $totalSum = 0.0;
        foreach ($this->receipts as $receipt) {
            if ($receipt->getStatus() === Receipt::STATUS_REFUNDED) {
                $totalSum += $receipt->getSum();
            }
        }

        return $totalSum;
    }

    public function getTotalRejectedReceiptSum(): float
    {
        $totalSum = 0.0;
        foreach ($this->receipts as $receipt) {
            if ($receipt->getStatus() === Receipt::STATUS_REJECTED) {
                $totalSum += $receipt->getSum();
            }
        }

        return $totalSum;
    }

    /**
     * @return string|null
     */
    public function getCompanyEmail()
    {
        return $this->companyEmail;
    }

    /**
     * @param string $companyEmail
     */
    public function setCompanyEmail($companyEmail)
    {
        $this->companyEmail = $companyEmail;
    }

    /**
     * @return ExecutiveBoardMembership[]
     */
    public function getExecutiveBoardMemberships()
    {
        return $this->executiveBoardMemberships;
    }

    /**
     * @return ExecutiveBoardMembership[]
     */
    public function getActiveExecutiveBoardMemberships()
    {
        $activeExecutiveBoardMemberships = [];
        if ($this->executiveBoardMemberships !== null) {
            foreach ($this->executiveBoardMemberships as $executiveBoardMembership) {
                if ($executiveBoardMembership->isActive()) {
                    $activeExecutiveBoardMemberships[] = $executiveBoardMembership;
                }
            }
        }

        return $activeExecutiveBoardMemberships;
    }

    /**
     * @return TeamMembership[]
     */
    public function getActiveTeamMemberships()
    {
        $activeTeamMemberships = [];
        if ($this->teamMemberships !== null) {
            foreach ($this->teamMemberships as $teamMembership) {
                if ($teamMembership->isActive()) {
                    $activeTeamMemberships[] = $teamMembership;
                }
            }
        }

        return $activeTeamMemberships;
    }

    public function getReservedFromPopUp(): bool
    {
        return $this->reservedFromPopUp;
    }

    public function setReservedFromPopUp(bool $reservedFromPopUp): void
    {
        $this->reservedFromPopUp = $reservedFromPopUp;
    }

    public function getLastPopUpTime(): \DateTime
    {
        return $this->lastPopUpTime;
    }

    /**
     * @param \DateTime $lastPopUpTime
     */
    public function setLastPopUpTime($lastPopUpTime): void
    {
        $this->lastPopUpTime = $lastPopUpTime;
    }

    /**
     * @return TeamMembershipInterface[]
     */
    public function getActiveMemberships()
    {
        return array_merge($this->getActiveTeamMemberships(), $this->getActiveExecutiveBoardMemberships());
    }

    /**
     * @param TeamMembershipInterface[] $memberships
     *
     * @return User $this
     */
    public function setMemberships($memberships)
    {
        $teamMemberships = [];
        $boardMemberships = [];
        foreach ($memberships as $membership) {
            if ($membership->getTeam()->getType() == 'team') {
                $teamMemberships[] = $membership;
            }
            if ($membership->getTeam()->getType() == 'executive_board') {
                $boardMemberships[] = $membership;
            }
        }

        $this->teamMemberships = $teamMemberships;
        $this->executiveBoardMemberships = $boardMemberships;

        return $this;
    }

    public function isAdmin(): bool
    {
        foreach ($this->roles as $role) {
            if ($role->getRole() === Roles::ADMIN) {
                return true;
            }
        }

        return false;
    }

    public function getUserIdentifier(): string
    {
        return $this->user_name ?? '';
    }

    public function isEqualTo(UserInterface $user): bool
    {
        return $this->password === $user->getPassword() && $this->user_name === $user->getUserIdentifier();
    }
}
