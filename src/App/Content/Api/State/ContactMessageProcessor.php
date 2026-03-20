<?php

namespace App\Content\Api\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Content\Api\Resource\ContactMessageInput;
use App\Organization\Infrastructure\Repository\DepartmentRepository;
use App\Support\Infrastructure\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\RateLimiter\RateLimiterFactory;

class ContactMessageProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly DepartmentRepository $departmentRepo,
        private readonly MailerInterface $mailer,
        private readonly RateLimiterFactory $contactFormLimiter,
        private readonly RequestStack $requestStack,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        assert($data instanceof ContactMessageInput);

        $limiter = $this->contactFormLimiter->create($this->requestStack->getCurrentRequest()?->getClientIp() ?? 'unknown');
        if (false === $limiter->consume(1)->isAccepted()) {
            throw new TooManyRequestsHttpException();
        }

        $department = $this->departmentRepo->find($data->departmentId);
        if (!$department) {
            throw new UnprocessableEntityHttpException('Department not found.');
        }

        $email = (new Email())
            ->from(new Address('noreply@vektorprogrammet.no', 'Vektorprogrammet'))
            ->replyTo($data->email)
            ->to($department->getEmail())
            ->subject('[Kontaktskjema] '.$data->subject)
            ->text(sprintf(
                "Navn: %s\nE-post: %s\n\n%s",
                $data->name,
                $data->email,
                $data->message
            ));

        $this->mailer->send($email);
    }
}
