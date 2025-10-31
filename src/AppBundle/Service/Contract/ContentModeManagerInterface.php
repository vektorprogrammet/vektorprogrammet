<?php

namespace AppBundle\Service\Contract;

/**
 * Interface for ContentModeManager service.
 * Defines contract for content mode management operations.
 */
interface ContentModeManagerInterface
{
    /**
     * Check if in edit mode.
     *
     * @return bool
     */
    public function isEditMode();

    /**
     * Change to edit mode.
     */
    public function changeToEditMode();

    /**
     * Change to read mode.
     */
    public function changeToReadMode();
}
