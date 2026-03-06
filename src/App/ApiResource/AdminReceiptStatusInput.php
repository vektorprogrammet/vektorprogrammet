<?php

namespace App\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Put;
use App\State\AdminReceiptStatusProcessor;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    operations: [
        new Put(
            uriTemplate: '/admin/receipts/{id}/status',
            processor: AdminReceiptStatusProcessor::class,
            security: "is_granted('ROLE_TEAM_LEADER')",
            read: false,
            output: false,
            status: 204,
        ),
    ],
)]
class AdminReceiptStatusInput
{
    #[Assert\NotBlank]
    #[Assert\Choice(choices: ['pending', 'refunded', 'rejected'])]
    public string $status = '';
}
