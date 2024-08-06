<?php

namespace MrAndMrsSmith\IdempotentConsumerBundle\Message;

class IncomingMessage
{
    /** @var mixed[] */
    private $payload;

    /** @var mixed[] */
    private $headers;

    /** @var string */
    private $name;

    /**
     * @param mixed[] $payload
     * @param mixed[] $headers
     */
    public function __construct(
        array $payload,
        array $headers,
        string $name
    ) {
        $this->payload = $payload;
        $this->headers = $headers;
        $this->name = $name;
    }

    /** @return mixed[] */
    public function getPayload(): array
    {
        return $this->payload;
    }

    /** @return mixed[] */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
