<?php

namespace App\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\State\AdminInterviewSchemaCreateProcessor;
use App\State\AdminInterviewSchemaEditProcessor;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    shortName: 'AdminInterviewSchemaWrite',
    operations: [
        new Post(
            uriTemplate: '/admin/interview-schemas',
            processor: AdminInterviewSchemaCreateProcessor::class,
            security: "is_granted('ROLE_TEAM_MEMBER')",
            status: 201,
        ),
        new Put(
            uriTemplate: '/admin/interview-schemas/{id}',
            processor: AdminInterviewSchemaEditProcessor::class,
            security: "is_granted('ROLE_TEAM_MEMBER')",
            read: false,
        ),
    ],
)]
class AdminInterviewSchemaWriteResource
{
    public ?int $id = null;

    #[Assert\NotBlank(message: 'Name cannot be blank.')]
    public ?string $name = null;

    /** @var array<array{question: string, type: string, helpText?: string, alternatives?: string[]}>|null */
    public ?array $questions = null;
}
