<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\ApiResource\ContactMessageInput;
use App\Entity\Repository\DepartmentRepository;
use App\Mailer\MailerInterface;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

class ContactMessageProcessor implements ProcessorInterface
{
    public function __construct(
        private DepartmentRepository $departmentRepo,
        private MailerInterface $mailer,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        assert($data instanceof ContactMessageInput);

        $department = $this->departmentRepo->find($data->departmentId);
        if (!$department) {
            throw new UnprocessableEntityHttpException('Department not found.');
        }

        $email = (new Email())
            ->from(new Address('noreply@vektorprogrammet.no', 'Vektorprogrammet'))
            ->replyTo($data->email)
            ->to($department->getEmail())
            ->subject('[Kontaktskjema] ' . $data->subject)
            ->text(sprintf(
                "Navn: %s\nE-post: %s\n\n%s",
                $data->name,
                $data->email,
                $data->message
            ));

        $this->mailer->send($email);
    }
}
