<?php

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;
use SymfonyCasts\Bundle\VerifyEmail\Model\VerifyEmailSignatureComponents;

class VerificationService
{
    public function __construct(
        private VerifyEmailHelperInterface $verifyEmailHelper
    ) {}

    public function generateURL(User $user): VerifyEmailSignatureComponents
    {
        // generate singed URL for email verification
        // this will generate a URL with a signature that is valid for 1 hour (verfiy_email.yaml)
        return $this->verifyEmailHelper->generateSignature(
            'api_verify_email', // route that handles the email verification
            $user->getId(), // used in signature generation, ensure this is unique to the user
            $user->getEmail(), // used in signature generation, ensure this is unique to the user
            ['id' => $user->getId()] // extra param to be added to the generated URL
        );
    }

    public function verifyEmail(
        User $user,
        Request $request,
        EntityManagerInterface $em
    ): void {
        $this->verifyEmailHelper->validateEmailConfirmationFromRequest(
            $request,
            $user->getId(),
            $user->getEmail()
        );
        $user->setIsVerified(true);
        $em->flush();
    }
}
