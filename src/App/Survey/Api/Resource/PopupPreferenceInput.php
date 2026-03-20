<?php

namespace App\Survey\Api\Resource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Put;
use App\Survey\Api\State\PopupPreferenceProcessor;

#[ApiResource(
    operations: [
        new Put(
            uriTemplate: '/me/popup-preference',
            processor: PopupPreferenceProcessor::class,
            security: "is_granted('ROLE_USER')",
            output: false,
            status: 204,
        ),
    ],
)]
class PopupPreferenceInput
{
}
