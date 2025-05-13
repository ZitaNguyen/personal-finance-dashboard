<?php

namespace App\Service;

use App\Entity\User;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Address;
use SymfonyCasts\Bundle\VerifyEmail\Model\VerifyEmailSignatureComponents;

class EmailService
{
    public function __construct(
        private MailerInterface $mailer,
        private LoggerInterface $logger,
    ) {}

    public function sendVerificationEmail(
        User $recipient, 
        VerifyEmailSignatureComponents $signature
    ): void
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
        try {
            $this->mailer->send($email);
            $this->logger->info('Verification email sent to ' . $recipient->getEmail());
        } catch (\Exception $e) {
            $this->logger->error('Error sending email: ' . $e->getMessage());
        }
        
    }
}
