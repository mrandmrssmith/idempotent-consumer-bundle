<?php

namespace MrAndMrsSmith\IdempotentConsumerBundle\Message;

class MessageStatus
{
    private const STATUS_FINISHED = 'FINISHED';
    private const STATUS_IN_PROGRESS = 'IN_PROGRESS';
    private const STATUS_NEW = 'NEW';
    private const STATUS_FAILED = 'FAILED';

    private $status;

    private $messageIdempotentKey;

    private $messageName;

    public function __construct(
        string $status,
        string $messageIdempotentKey,
        string $messageName
    ) {
        $this->status = $status;
        $this->messageIdempotentKey = $messageIdempotentKey;
        $this->messageName = $messageName;
    }

    public static function createNew(
        string $messageIdempotentKey,
        string $messageName
    ): self {
        return new self(self::STATUS_NEW, $messageIdempotentKey, $messageName);
    }

    public function getIdempotentKey(): string
    {
        return $this->messageIdempotentKey;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getMessageName(): string
    {
        return $this->messageName;
    }

    public function statusAllowProcessing(): bool
    {
        return $this->status === self::STATUS_NEW;
    }

    public function fail()
    {
        $this->status = self::STATUS_FAILED;
    }

    public function finish()
    {
        $this->status = self::STATUS_FINISHED;
    }

    public function start()
    {
        $this->status = self::STATUS_IN_PROGRESS;
    }
}
