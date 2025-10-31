<?php

namespace AppBundle\Service\Contract;

use AppBundle\Entity\AdmissionPeriod;
use AppBundle\Entity\Application;
use AppBundle\Entity\Department;
use AppBundle\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Interface for ApplicationAdmission service.
 * Defines contract for application admission operations.
 */
interface ApplicationAdmissionInterface
{
    /**
     * Create application for existing assistant.
     *
     * @param User $user
     * @return Application
     */
    public function createApplicationForExistingAssistant(User $user): Application;

    /**
     * Check if user has already applied.
     *
     * @param User $user
     * @return bool
     */
    public function userHasAlreadyApplied(User $user);

    /**
     * Check if user has already applied in admission period.
     *
     * @param User $user
     * @param AdmissionPeriod $admissionPeriod
     * @return bool
     */
    public function userHasAlreadyAppliedInAdmissionPeriod(User $user, AdmissionPeriod $admissionPeriod);

    /**
     * Set correct user for application.
     *
     * @param Application $application
     */
    public function setCorrectUser(Application $application);

    /**
     * Get existing assistant login message.
     *
     * @return string
     */
    public function getExistingAssistantLoginMessage(): string;

    /**
     * Get department from request.
     *
     * @param Request $request
     * @return Department
     */
    public function getDepartment(Request $request): Department;

    /**
     * Render error page.
     *
     * @param User|null $user
     * @return Response|null
     */
    public function renderErrorPage(User $user = null);
}
