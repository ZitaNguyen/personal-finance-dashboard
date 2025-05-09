<?php

namespace App\Service;

use App\Entity\User;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Address;
use SymfonyCasts\Bundle\VerifyEmail\Model\VerifyEmailSignatureComponents;

class EmailService
{
    public function __construct(private MailerInterface $mailer) {}

    public function sendVerificationEmail(User $recipient, VerifyEmailSignatureComponents $signature): void
    {
        $email = (new TemplatedEmail())
            ->from(new Address('no-reply@test.com', 'Test Personal Finance Dashboard App'))
            ->to($recipient->getEmail())
            ->subject('Please confirm your email')
            ->htmlTemplate('emails/registration.html.twig')
            ->context([
                'user' => $recipient,
                'signedUrl' => $signature->getSignedUrl(),
            ]);

        $this->mailer->send($email);
    }
}
