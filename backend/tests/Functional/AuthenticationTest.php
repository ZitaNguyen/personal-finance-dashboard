<?php

namespace App\Tests\Functional;

use App\Entity\User;
use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use Hautelook\AliceBundle\PhpUnit\ReloadDatabaseTrait;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AuthenticationTest extends ApiTestCase
{
    use ReloadDatabaseTrait; // reload and clear the database before each test, 

    public function testUserCanLogin(): void
    {
        $client = self::createClient([], [
            'base_uri' => 'http://localhost:8080',
        ]);
        $container = self::getContainer();

        $user = new User();
        $user->setEmail('test@example.com');
        $user->setRoles(['ROLE_USER']);
        $user->setUsername('tester');

        /** @var UserPasswordHasherInterface $passwordHasher */
        $passwordHasher = $container->get(UserPasswordHasherInterface::class);
        $hashedPassword = $passwordHasher->hashPassword($user, 'password');
        $user->setPassword($hashedPassword);

        $manager = $container->get('doctrine')->getManager();
        $manager->persist($user);
        $manager->flush();

        // retrieve a token
        $response = $client->request('POST', '/api/auth', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'email' => 'test@example.com',
                'password' => 'password'
            ]
        ]);

        $json = $response->toArray(false); // avoid throwing an exception on error
        $this->assertResponseIsSuccessful();
        $this->assertArrayHasKey('token', $json);
    }

    public function testLoginFailsWithWrongPassword(): void
    {
        $client = self::createClient([], [
            'base_uri' => 'http://localhost:8080',
        ]);
        $container = self::getContainer();

        $user = new User();
        $user->setEmail('test2@example.com');
        $user->setRoles(['ROLE_USER']);
        $user->setUsername('tester2');

        /** @var UserPasswordHasherInterface $passwordHasher */
        $passwordHasher = $container->get(UserPasswordHasherInterface::class);
        $hashedPassword = $passwordHasher->hashPassword($user, 'password2');
        $user->setPassword($hashedPassword);

        $manager = $container->get('doctrine')->getManager();
        $manager->persist($user);
        $manager->flush();

        // retrieve a token
        $response = $client->request('POST', '/api/auth', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'email' => 'test2@example.com',
                'password' => 'password'
            ]
        ]);
        
        $json = $response->toArray(false);
        $this->assertResponseStatusCodeSame(401);
        $this->assertArrayHasKey('message', $json);
        $this->assertEquals('Invalid credentials.', $json['message']);
    }

    public function testUserCanRegister(): void
    {
        $client = self::createClient([], [
            'base_uri' => 'http://localhost:8080',
        ]);

        $response = $client->request('POST', '/api/register', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'email' => 'zita@test.fr',
                'password' => 'password',
                'username' => 'zita'
            ]
        ]);
        $json = $response->toArray(false);
        $this->assertResponseIsSuccessful();
        $this->assertArrayHasKey('message', $json);
        $this->assertEquals('User registered successfully', $json['message']);
        $this->assertResponseStatusCodeSame(201);
    }

    public function testUserCannotRegisterWithInvalidEmail(): void
    {
        $client = self::createClient([], [
            'base_uri' => 'http://localhost:8080',
        ]);

        $response = $client->request('POST', '/api/register', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'email' => 'invalid-email',
                'password' => 'password',
                'username' => 'zita'
            ]
        ]);
        $json = $response->toArray(false);
        $this->assertResponseStatusCodeSame(400);
        $this->assertArrayHasKey('errors', $json);
    }

    public function testUserCannotRegisterWithEmptyEmail(): void
    {
        $client = self::createClient([], [
            'base_uri' => 'http://localhost:8080',
        ]);

        $response = $client->request('POST', '/api/register', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'email' => '',
                'password' => 'password',
                'username' => 'zita'
            ]
        ]);
        $json = $response->toArray(false);
        $this->assertResponseStatusCodeSame(400);
        $this->assertArrayHasKey('errors', $json);
        $this->assertStringContainsString('This value should not be blank', $json['errors']);
    }

    public function testUserCannotRegisterWithEmptyPassword(): void
    {
        $client = self::createClient([], [
            'base_uri' => 'http://localhost:8080',
        ]);

        $response = $client->request('POST', '/api/register', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'email' => 'test3@test.fr',
                'password' => '',
                'username' => 'zita'
            ]
        ]);
        $json = $response->toArray(false);
        $this->assertResponseStatusCodeSame(400);
        $this->assertArrayHasKey('errors', $json);
        $this->assertStringContainsString('This value should not be blank', $json['errors']);
    }

    public function testUserCannotRegisterWithShortPassword(): void
    {
        $client = self::createClient([], [
            'base_uri' => 'http://localhost:8080',
        ]);

        $response = $client->request('POST', '/api/register', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'email' => 'test4@test.fr',
                'password' => 'short',
                'username' => 'zita'
            ]
        ]);
        $json = $response->toArray(false);
        $this->assertResponseStatusCodeSame(400);
        $this->assertArrayHasKey('errors', $json);
        $this->assertStringContainsString('This value is too short. It should have 6 characters or more.', $json['errors']);
    }
}