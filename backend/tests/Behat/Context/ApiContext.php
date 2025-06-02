<?php

namespace App\Tests\Behat\Context;

use Behat\Step\Then;
use Symfony\Component\HttpFoundation\Response;
use Behat\Step\When;
use Behat\Behat\Context\Context;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

class ApiContext implements Context
{

    /** @var KernelBrowser */
    private $client;

    /** @var Response|null */
    private $response;

    public function __construct(KernelBrowser $client)
    {
        $this->client = $client;
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
