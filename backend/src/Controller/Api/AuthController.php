<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Service\EmailService;
use App\Dto\RegisterUserInput;
use App\Service\VerificationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AuthController extends AbstractController
{

    public function __construct(
        private EntityManagerInterface $em,
        private VerificationService $verificationService,
    )
    {
        $this->em = $em;
        $this->verificationService = $verificationService;
    }
    
    #[Route('/api/register', name: 'api_register', methods: ['POST'])]
    public function register(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        ValidatorInterface $validator,
        EmailService $emailService,
    ): JsonResponse
    {
        // get the request data
        $data = json_decode($request->getContent(), true);
        $dto = new RegisterUserInput();
        $dto->email = $data['email'] ?? null;
        $dto->password = $data['password'] ?? null;
        $dto->username = $data['username'] ?? null;
        
        // validate the data
        $errors = $validator->validate($dto);
        if (count($errors) > 0) {
            return $this->json([
                'errors' => (string) $errors,
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        // prepare the user entity to be persisted
        $user = new User();
        $user->setEmail($dto->email);
        $user->setPassword(
            $passwordHasher->hashPassword($user, $dto->password)
        );
        $user->setUsername($dto->username);

        try {
            // persist user
            $this->em->persist($user);
            $this->em->flush();

            // generate signed URL for email verification
            $signature = $this->verificationService->generateURL($user);

            // send verification email
            $emailService->sendVerificationEmail($user, $signature);

            return $this->json([
                'message' => 'User registered successfully, please check your email to verify your account.',
            ], JsonResponse::HTTP_CREATED);
        
        } catch (\Exception $e) {
            if ($e instanceof UniqueConstraintViolationException) {
                return $this->json([
                    'errorMessage' => 'User registration failed. Email already exists.',
                ], JsonResponse::HTTP_CONFLICT);
            }
            // handle other exceptions
            return $this->json([
                'errorMessage' => 'User registration failed.' . $e->getMessage(),
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/api/verify-email', name: 'api_verify_email', methods: ['GET'])]
    public function verifyEmail(Request $request): JsonResponse
    {
        $userId = $request->query->get('id');
        $user = $this->em->getRepository(User::class)->find($userId);

        if (!$user) {
            return $this->json([
                'message' => 'User not found.',
            ], JsonResponse::HTTP_NOT_FOUND);
        }

        // verify the email
        try {
            $this->verificationService->verifyEmail($user, $request, $this->em);
            return $this->json([
                'message' => 'Email verified successfully.',
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'message' => 'Invalid or expired verification link.',
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

    }
}