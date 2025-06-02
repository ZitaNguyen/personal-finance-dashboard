<?php

namespace App\Tests\Behat\Context;

use Behat\Step\Then;
use Behat\Step\When;
use Behat\Behat\Context\Context;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\HttpKernel\KernelInterface;

class ApiContext implements Context
{
    /** @var KernelInterface */
    private $kernel;

    /** @var KernelBrowser */
    private $client;
    
    /** @var EntityManagerInterface */
    private $em;

    /** @var Response|null */
    private $response;

    public function __construct(
        KernelInterface $kernel,
        KernelBrowser $client, 
        EntityManagerInterface $em)
    {
        if ($kernel->getEnvironment() === 'prod') {
            throw new \RuntimeException('Behat tests are not allowed in the production environment!');
        }

        $this->client = $client;
        $this->em = $em;
    }

    /** @BeforeScenario @ResetDatabase */
    public function resetDatabase() 
    {
        $data = $this->em->getMetadataFactory()->getAllMetadata();

        $tool = new SchemaTool($this->em);
        $tool->dropDatabase($data);
        $tool->createSchema($data);
    }


    #[When('I send a POST request to :endpoint with body:')]
    public function iSendAPostRequestToWithBody(string $endpoint, string $body)
    {
        $this->client->request(
            'POST',
            $endpoint, // no need baseURL when using KernelBrowser
            [], // query parameters
            [], // files
            ['Content-Type' => 'application/json'],
            $body
        );
        $this->response = $this->client->getResponse();
    }

    #[Then('the response code should be :statusCode')]
    public function theResponseCodeShouldBe(int $statusCode)
    {
        if ($this->response->getStatusCode() !== $statusCode) {
            throw new \Exception("Expected $statusCode but got " . $this->response->getStatusCode());
        }
    }

    #[Then('the response should contain :text')]
    public function theResponseShouldContain(string $text)
    {
        $content = $this->response->getContent(false);
        if (strpos($content, $text) === false) {
            throw new \Exception("Response does not contain '$text'. Content was: $content");
        }
    }
}
