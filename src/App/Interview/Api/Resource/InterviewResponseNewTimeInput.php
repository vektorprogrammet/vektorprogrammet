<?php

declare(strict_types=1);

namespace App\Interview\Api\Resource;

use Symfony\Component\Validator\Constraints as Assert;

final class InterviewResponseNewTimeInput
{
    #[Assert\NotBlank]
    #[Assert\Length(max: 2000)]
    public string $newTimeMessage = '';
}
