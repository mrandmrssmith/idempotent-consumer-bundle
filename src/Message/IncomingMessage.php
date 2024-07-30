<?php

namespace MrAndMrsSmith\IdempotentConsumerBundle\Message;

class IncomingMessage
{
    private $payload;

    private $headers;

    private $name;

    public function __construct(
        array $payload,
        array $headers,
        string $name
    ) {
        $this->payload = $payload;
        $this->headers = $headers;
        $this->name = $name;
    }

    public function getPayload(): array
    {
        return $this->payload;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
