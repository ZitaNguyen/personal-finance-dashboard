<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private $userPasswordHasher;

    public function __construct(UserPasswordHasherInterface $userPasswordHasher)
    {
        $this->userPasswordHasher = $userPasswordHasher;
    }
    
    public function load(ObjectManager $manager): void
    {
        // create 20 users
        for ($i = 0; $i < 20; $i++) {
            $user = new User();
            $user->setUsername('User ' . $i);
            $user->setEmail('user' . $i . '@test.com');
            $user->setRoles(['ROLE_USER']);
            $user->setPassword($this->userPasswordHasher->hashPassword($user, 'password' . $i));
            $manager->persist($user);
        }

        $manager->flush();
    }
}
