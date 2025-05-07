<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Dto\RegisterUserInput;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AuthController extends AbstractController
{
    #[Route('/api/register', name: 'api_register', methods: ['POST'])]
    public function register(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $em,
        ValidatorInterface $validator
    ): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $dto = new RegisterUserInput();
        $dto->email = $data['email'] ?? null;
        $dto->password = $data['password'] ?? null;
        $dto->username = $data['username'] ?? null;
        
        $errors = $validator->validate($dto);
        if (count($errors) > 0) {
            return $this->json([
                'errors' => (string) $errors,
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        $user = new User();
        $user->setEmail($dto->email);
        $user->setPassword(
            $passwordHasher->hashPassword($user, $dto->password)
        );
        $user->setUsername($dto->username);

        $em->persist($user);
        $em->flush();

        return $this->json([
            'message' => 'User registered successfully',
        ], JsonResponse::HTTP_CREATED);
    }
}