<?php

namespace App\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\State\ReceiptCreateProcessor;
use App\State\ReceiptDeleteProcessor;
use App\State\ReceiptEditProcessor;
use App\State\ReceiptWriteProvider;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    operations: [
        new Post(
            uriTemplate: '/receipts',
            processor: ReceiptCreateProcessor::class,
            security: "is_granted('ROLE_USER')",
            status: 201,
        ),
        new Put(
            uriTemplate: '/receipts/{id}',
            provider: ReceiptWriteProvider::class,
            processor: ReceiptEditProcessor::class,
            security: "is_granted('ROLE_USER')",
            status: 200,
        ),
        new Delete(
            uriTemplate: '/receipts/{id}',
            provider: ReceiptWriteProvider::class,
            processor: ReceiptDeleteProcessor::class,
            security: "is_granted('ROLE_USER')",
            deserialize: false,
            validate: false,
            output: false,
            status: 204,
        ),
    ],
)]
class ReceiptWriteResource
{
    public ?int $id = null;

    #[Assert\NotBlank(message: 'Description is required.')]
    #[Assert\Length(max: 5000, maxMessage: 'Description must not exceed 5000 characters.')]
    public ?string $description = null;

    #[Assert\NotBlank(message: 'Sum is required.')]
    #[Assert\GreaterThan(value: 0, message: 'Sum must be greater than 0.')]
    public ?float $sum = null;

    #[Assert\Regex(pattern: '/^\d{4}-\d{2}-\d{2}$/', message: 'Date must be in YYYY-MM-DD format.')]
    public ?string $receiptDate = null;
}
