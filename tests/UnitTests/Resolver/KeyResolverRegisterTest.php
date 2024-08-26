<?php

namespace Tests\UnitTests\Resolver;

use MrAndMrsSmith\IdempotentConsumerBundle\Exception\KeyResolverNotFoundException;
use MrAndMrsSmith\IdempotentConsumerBundle\Message\IncomingMessage;
use MrAndMrsSmith\IdempotentConsumerBundle\Resolver\IdempotentKeyResolver;
use MrAndMrsSmith\IdempotentConsumerBundle\Resolver\KeyResolverRegister;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class KeyResolverRegisterTest extends TestCase
{
    /** @var IdempotentKeyResolver|MockObject */
    private $firstResolver;

    /** @var IdempotentKeyResolver|MockObject */
    private $secondResolver;

    /** @var KeyResolverRegister */
    private $register;

    public function setUp(): void
    {
        $this->firstResolver = $this->createMock(IdempotentKeyResolver::class);
        $this->secondResolver = $this->createMock(IdempotentKeyResolver::class);

        $this->register = new KeyResolverRegister(
            [
                $this->firstResolver,
                $this->secondResolver
            ]
        );
    }

    public function testWillThrowExceptionWhenSupportedResolverDoesNotExist(): void
    {
        $message = $this->createMock(IncomingMessage::class);

        $this->firstResolver
            ->expects($this->once())
            ->method('supports')
            ->with($message)
            ->willReturn(false);
        $this->secondResolver
            ->expects($this->once())
            ->method('supports')
            ->with($message)
            ->willReturn(false);
        $this->expectException(KeyResolverNotFoundException::class);

        $this->register->getResolver($message);
    }

    public function testWillReturnResolverWhenSupportedResolverExists(): void
    {
        $message = $this->createMock(IncomingMessage::class);

        $this->firstResolver
            ->expects($this->once())
            ->method('supports')
            ->with($message)
            ->willReturn(false);
        $this->secondResolver
            ->expects($this->once())
            ->method('supports')
            ->with($message)
            ->willReturn(true);

        $this->assertSame($this->secondResolver, $this->register->getResolver($message));
    }
}
