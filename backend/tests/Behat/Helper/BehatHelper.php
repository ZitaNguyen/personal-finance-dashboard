<?php

namespace App\Tests\Behat\Helper;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\EventListener\MessageLoggerListener;

class BehatHelper
{
    private KernelBrowser $client;
    private ?Response $response = null;

    public function __construct(KernelBrowser $client)
    {
        $this->client = $client;
    }

    public function sendGETRequest(string $url): Response
    {
        $this->client->request('GET', $url);

        return $this->response = $this->client->getResponse();
    }

    public function sendPOSTRequest(string $url, string|array $body): Response
    {
        $body = is_array($body) ? json_encode($body) : $body;

        $this->client->request(
            'POST',
            $url,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            $body
        );

        return $this->response = $this->client->getResponse();
    }

    public function assertStatusCode(int $expected): void
    {
        $actual = $this->response->getStatusCode();
        if ($actual !== $expected) {
            throw new \Exception("Expected status $expected, got $actual. Response: " . $this->response->getContent(false));
        }
    }

    public function assertResponseContains(string $needle): void
    {
        $content = $this->response->getContent(false);
        if (strpos($content, $needle) === false) {
            throw new \Exception("Response does not contain '$needle'. Full response: $content");
        }
    }

    public function extractVerificationLink(): string
    {
        $container = $this->client->getContainer();

        /** @var MessageLoggerListener $logger */
        $logger = $container->get(MessageLoggerListener::class);
        $events = $logger->getEvents();

        if (empty($events)) {
            throw new \Exception("No email events found.");
        }

        $messages = $events->getMessages();
        $body = $messages[0]->getHtmlBody();

        preg_match('/https?:\/\/[^"]+\/api\/verify-email\?[^"]+/', $body, $matches);
        $link = html_entity_decode($matches[0] ?? '');

        if (!$link) {
            throw new \Exception("Verification link not found in email.");
        }

        return $link;
    }
}
