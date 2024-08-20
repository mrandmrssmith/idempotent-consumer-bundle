<?php

namespace Tests\UnitTests\Checker;

use MrAndMrsSmith\IdempotentConsumerBundle\Checker\CheckMessageCanBeProcessed;
use MrAndMrsSmith\IdempotentConsumerBundle\Message\IncomingMessage;
use MrAndMrsSmith\IdempotentConsumerBundle\Message\MessageStatus;
use MrAndMrsSmith\IdempotentConsumerBundle\Persistence\MessageStatusPersister;
use MrAndMrsSmith\IdempotentConsumerBundle\Persistence\MessageStatusRetriever;
use MrAndMrsSmith\IdempotentConsumerBundle\Persistence\MessageStatusUpdater;
use MrAndMrsSmith\IdempotentConsumerBundle\ProcessFailedMessageVoter\ProcessFailedMessageVoter;
use MrAndMrsSmith\IdempotentConsumerBundle\Resolver\IdempotentKeyResolver;
use MrAndMrsSmith\IdempotentConsumerBundle\Resolver\KeyResolverRegister;
use PHPUnit\Framework\Constraint\IsEqual;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class CheckMessageCanBeProcessedTest extends TestCase
{
    private const MESSAGE_KEY = 'key';

    private const MESSAGE_NAME = 'name';

    /** @var KeyResolverRegister|MockObject */
    private $idempotentKeyResolversRegister;

    /** @var MessageStatusRetriever|MockObject */
    private $messageStatusRetriever;

    /** @var MessageStatusPersister|MockObject */
    private $messageStatusPersister;

    /** @var MessageStatusUpdater|MockObject */
    private $messageStatusUpdater;

    /** @var ProcessFailedMessageVoter|MockObject */
    private $processFailedMessageVoter;

    /** @var IdempotentKeyResolver|MockObject */
    private $resolver;

    /** @var CheckMessageCanBeProcessed */
    private $checker;

    public function setUp(): void
    {
        $this->idempotentKeyResolversRegister = $this->createMock(KeyResolverRegister::class);
        $this->messageStatusRetriever = $this->createMock(MessageStatusRetriever::class);
        $this->messageStatusPersister = $this->createMock(MessageStatusPersister::class);
        $this->messageStatusUpdater = $this->createMock(MessageStatusUpdater::class);
        $this->processFailedMessageVoter = $this->createMock(ProcessFailedMessageVoter::class);
        $this->resolver = $this->createMock(IdempotentKeyResolver::class);


        $this->checker = new CheckMessageCanBeProcessed(
            $this->idempotentKeyResolversRegister,
            $this->messageStatusRetriever,
            $this->messageStatusPersister,
            $this->messageStatusUpdater,
            $this->processFailedMessageVoter
        );
    }

    public function testWhenNewMessage(): void
    {
        $incomingMessage = $this->createMock(IncomingMessage::class);
        $incomingMessage
            ->method('getName')
            ->willReturn(self::MESSAGE_NAME);
        $newMessageStatus = MessageStatus::createNew(
            self::MESSAGE_KEY,
            self::MESSAGE_NAME
        );
        $startedMessage = clone $newMessageStatus;
        $startedMessage->start();

        $this->idempotentKeyResolversRegister
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
            ->willReturn(null);;
        $this->messageStatusPersister
            ->expects($this->once())
            ->method('persist')
            ->with(new IsEqual(
                $newMessageStatus
            ));
        $this->messageStatusUpdater
            ->expects($this->once())
            ->method('update')
            ->with(new IsEqual(
                $startedMessage
            ));

        $this->assertTrue($this->checker->check($incomingMessage));
    }

    public function testWhenMessageStatusNotAllowsProcessingAndIsNotFailed(): void
    {
        $incomingMessage = $this->createMock(IncomingMessage::class);
        $incomingMessage
            ->method('getName')
            ->willReturn(self::MESSAGE_NAME);
        $messageStatus = MessageStatus::createNew(
            self::MESSAGE_KEY,
            self::MESSAGE_NAME
        );
        $messageStatus->start();
        $messageStatus->finish();

        $this->idempotentKeyResolversRegister
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

        $this->assertFalse($this->checker->check($incomingMessage));
    }

    public function testWhenMessageStatusFailedAndVoterReturnTrue(): void
    {
        $incomingMessage = $this->createMock(IncomingMessage::class);
        $incomingMessage
            ->method('getName')
            ->willReturn(self::MESSAGE_NAME);
        $messageStatus = MessageStatus::createNew(
            self::MESSAGE_KEY,
            self::MESSAGE_NAME
        );
        $startedMessage = clone $messageStatus;
        $startedMessage->start();
        $messageStatus->start();
        $messageStatus->fail();

        $this->idempotentKeyResolversRegister
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
        $this->processFailedMessageVoter
            ->expects($this->once())
            ->method('vote')
            ->with($incomingMessage)
            ->willReturn(true);
        $this->messageStatusUpdater
            ->expects($this->once())
            ->method('update')
            ->with(new IsEqual(
                $startedMessage
            ));

        $this->assertTrue($this->checker->check($incomingMessage));
    }

    public function testWhenMessageExistsAndStatusAllowProcessing(): void
    {
        $incomingMessage = $this->createMock(IncomingMessage::class);
        $incomingMessage
            ->method('getName')
            ->willReturn(self::MESSAGE_NAME);
        $messageStatus = MessageStatus::createNew(
            self::MESSAGE_KEY,
            self::MESSAGE_NAME
        );
        $startedMessage = clone $messageStatus;
        $startedMessage->start();

        $this->idempotentKeyResolversRegister
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
        $this->processFailedMessageVoter
            ->expects($this->never())
            ->method('vote');
        $this->messageStatusUpdater
            ->expects($this->once())
            ->method('update')
            ->with(new IsEqual(
                $startedMessage
            ));

        $this->assertTrue($this->checker->check($incomingMessage));
    }

    public function testWhenMessageFailedAndVoterReturnsFalse(): void
    {
        $incomingMessage = $this->createMock(IncomingMessage::class);
        $incomingMessage
            ->method('getName')
            ->willReturn(self::MESSAGE_NAME);
        $messageStatus = MessageStatus::createNew(
            self::MESSAGE_KEY,
            self::MESSAGE_NAME
        );
        $messageStatus->start();
        $messageStatus->fail();

        $this->idempotentKeyResolversRegister
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
        $this->processFailedMessageVoter
            ->expects($this->once())
            ->method('vote')
            ->with($incomingMessage)
            ->willReturn(false);
        $this->messageStatusUpdater
            ->expects($this->never())
            ->method('update');

        $this->assertFalse($this->checker->check($incomingMessage));
    }
}
