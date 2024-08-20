<?php

namespace Tests\UnitTests\Finalizer;

use MrAndMrsSmith\IdempotentConsumerBundle\Exception\MessageStatusDoesNotExistException;
use MrAndMrsSmith\IdempotentConsumerBundle\Finalizer\MessageFinalizer;
use MrAndMrsSmith\IdempotentConsumerBundle\Message\IncomingMessage;
use MrAndMrsSmith\IdempotentConsumerBundle\Message\MessageStatus;
use MrAndMrsSmith\IdempotentConsumerBundle\Persistence\MessageStatusRetriever;
use MrAndMrsSmith\IdempotentConsumerBundle\Persistence\MessageStatusUpdater;
use MrAndMrsSmith\IdempotentConsumerBundle\Resolver\IdempotentKeyResolver;
use MrAndMrsSmith\IdempotentConsumerBundle\Resolver\KeyResolverRegister;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class MessageFinalizerTest extends TestCase
{
    private const MESSAGE_KEY = 'message_key';

    /** @var MessageStatusUpdater|MockObject */
    private $messageUpdater;

    /** @var KeyResolverRegister|MockObject */
    private $keyResolverRegister;

    /** @var MessageStatusRetriever|MockObject */
    private $messageStatusRetriever;

    /** @var IdempotentKeyResolver|MockObject */
    private $resolver;

    /** @var MessageFinalizer */
    private $finalizer;

    public function setUp(): void
    {
        $this->messageUpdater = $this->createMock(MessageStatusUpdater::class);
        $this->keyResolverRegister = $this->createMock(KeyResolverRegister::class);
        $this->messageStatusRetriever = $this->createMock(MessageStatusRetriever::class);
        $this->resolver = $this->createMock(IdempotentKeyResolver::class);

        $this->finalizer = new MessageFinalizer(
            $this->messageUpdater,
            $this->keyResolverRegister,
            $this->messageStatusRetriever
        );
    }

    public function testFinalizeWillThrowMessageStatusDoesNotExistException(): void
    {
        $incomingMessage = $this->createMock(IncomingMessage::class);

        $this->keyResolverRegister
            ->expects($this->once())
            ->method('getResolver')
            ->with($incomingMessage)
            ->willReturn($this->resolver);
        $this->resolver
            ->expects($this->once())
            ->method('resolveKey')
            ->with($incomingMessage)
            ->willReturn(self::MESSAGE_KEY);
        $this->messageStatusRetriever
            ->expects($this->once())
            ->method('retrieve')
            ->with(self::MESSAGE_KEY)
            ->willReturn(null);

        $this->expectException(MessageStatusDoesNotExistException::class);

        $this->finalizer->finalizeSuccess($incomingMessage);
    }

    public function testFinalizeSuccess(): void
    {
        $incomingMessage = $this->createMock(IncomingMessage::class);
        $messageStatus = $this->getMessageStatusMock($incomingMessage);

        $messageStatus
            ->expects($this->once())
            ->method('finish');
        $this->messageUpdater
            ->expects($this->once())
            ->method('update')
            ->with($messageStatus);

        $this->finalizer->finalizeSuccess($incomingMessage);
    }

    public function testFinalizeFailure(): void
    {
        $incomingMessage = $this->createMock(IncomingMessage::class);
        $messageStatus = $this->getMessageStatusMock($incomingMessage);

        $messageStatus
            ->expects($this->once())
            ->method('fail');
        $this->messageUpdater
            ->expects($this->once())
            ->method('update')
            ->with($messageStatus);

        $this->finalizer->finalizeFailure($incomingMessage);
    }

    public function testMarkAsRetry(): void
    {
        $incomingMessage = $this->createMock(IncomingMessage::class);
        $messageStatus = $this->getMessageStatusMock($incomingMessage);

        $messageStatus
            ->expects($this->once())
            ->method('markAsRetry');
        $this->messageUpdater
            ->expects($this->once())
            ->method('update')
            ->with($messageStatus);

        $this->finalizer->markAsRetry($incomingMessage);
    }

    /** @return MessageStatus|MockObject */
    private function getMessageStatusMock(IncomingMessage $incomingMessage): MessageStatus
    {
        $messageStatus = $this->createMock(MessageStatus::class);

        $this->keyResolverRegister
            ->expects($this->once())
            ->method('getResolver')
            ->with($incomingMessage)
            ->willReturn($this->resolver);
        $this->resolver
            ->expects($this->once())
            ->method('resolveKey')
            ->with($incomingMessage)
            ->willReturn(self::MESSAGE_KEY);
        $this->messageStatusRetriever
            ->expects($this->once())
            ->method('retrieve')
            ->with(self::MESSAGE_KEY)
            ->willReturn($messageStatus);

        return $messageStatus;
    }
}
