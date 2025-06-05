<?php

namespace App\Tests\Behat\Context;

use App\Tests\Behat\Helper\BehatHelper;
use Behat\Step\Then;
use Behat\Step\When;
use Behat\Step\Given;
use Behat\Behat\Context\Context;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\HttpKernel\KernelInterface;

class AuthContext implements Context
{
    private BehatHelper $helper;
    private string $verificationLink;

    private const API_REGISTER = '/api/register';
    private const API_LOGIN = '/api/auth';

    public function __construct(
        private KernelInterface $kernel,
        private KernelBrowser $client, 
        private EntityManagerInterface $em)
    {
        if ($this->kernel->getEnvironment() === 'prod') {
            throw new \RuntimeException('Behat tests are not allowed in the production environment!');
        }
        // $this->client->setServerParameters([
        //     'HTTP_HOST' => 'localhost:8080',
        // ]);
        $this->helper = new BehatHelper($this->client);
    }

    /** @BeforeScenario @ResetDatabase */
    public function resetDatabase() 
    {
        $data = $this->em->getMetadataFactory()->getAllMetadata();
        $tool = new SchemaTool($this->em);
        $tool->dropDatabase($data);
        $tool->createSchema($data);
    }

    #[Given('I register with body:')]
    public function register(string $body)
    {
        $this->helper->sendPOSTRequest(self::API_REGISTER, $body);
    }

    #[Given('I receive a message confirming successful registration')]
    public function confirmRegistration()
    {
        $this->helper->assertStatusCode(201);
    }

    #[Then('I should receive a verification email')]
    public function checkVerificationEmail()
    {
        $this->verificationLink = $this->helper->extractVerificationLink();
        print_r($this->verificationLink);
    }

    #[When('I visit the verification link from the email')]
    public function visitVerificationLink()
    {   
        $this->helper->sendGETRequest($this->verificationLink);
    }

    #[When('I connect with this account:')]
    public function connectUser(string $body)
    {
        $this->helper->sendPOSTRequest(self::API_LOGIN, $body);
    }

    #[Then('the response code should be :statusCode')]
    public function getResponseStatusCode(int $statusCode)
    {
        $this->helper->assertStatusCode($statusCode);
    }

    #[Then('the response should contain :text')]
    public function getResponseMessage(string $text)
    {
        $this->helper->assertResponseContains($text);
    }
}
