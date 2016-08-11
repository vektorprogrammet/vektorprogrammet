<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="interview_score")
 */
class InterviewScore
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank(groups={"interview"}, message="Dette feltet kan ikke være tomt.")
     */
    protected $explanatoryPower;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank(groups={"interview"}, message="Dette feltet kan ikke være tomt.")
     */
    protected $roleModel;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank(groups={"interview"}, message="Dette feltet kan ikke være tomt.")
     */
    protected $suitability;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank(groups={"interview"}, message="Dette feltet kan ikke være tomt.")
     */
    private $suitableAssistant;

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
     * Set explanatoryPower.
     *
     * @param int $explanatoryPower
     *
     * @return InterviewScore
     */
    public function setExplanatoryPower($explanatoryPower)
    {
        $this->explanatoryPower = $explanatoryPower;

        return $this;
    }

    /**
     * Get explanatoryPower.
     *
     * @return int
     */
    public function getExplanatoryPower()
    {
        return $this->explanatoryPower;
    }

    /**
     * Set roleModel.
     *
     * @param int $roleModel
     *
     * @return InterviewScore
     */
    public function setRoleModel($roleModel)
    {
        $this->roleModel = $roleModel;

        return $this;
    }

    /**
     * Get roleModel.
     *
     * @return int
     */
    public function getRoleModel()
    {
        return $this->roleModel;
    }

    /**
     * Set suitability.
     *
     * @param int $suitability
     *
     * @return InterviewScore
     */
    public function setSuitability($suitability)
    {
        $this->suitability = $suitability;

        return $this;
    }

    /**
     * Get suitability.
     *
     * @return int
     */
    public function getSuitability()
    {
        return $this->suitability;
    }

    /**
     * Get the sum of all the scores.
     *
     * @return int
     */
    public function getSum()
    {
        return $this->explanatoryPower + $this->roleModel + $this->suitability;
    }

    /**
     * @return string
     */
    public function getSuitableAssistant()
    {
        return $this->suitableAssistant;
    }

    /**
     * @param string $suitableAssistant
     */
    public function setSuitableAssistant($suitableAssistant)
    {
        $this->suitableAssistant = $suitableAssistant;
    }

    public function hideScores()
    {
        $this->setDrive(0);
        $this->setExplanatoryPower(0);
        $this->setRoleModel(0);
        $this->setTotalImpression(0);
    }
}
