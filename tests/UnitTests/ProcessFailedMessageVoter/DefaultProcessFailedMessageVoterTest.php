<?php

namespace Tests\UnitTests\ProcessFailedMessageVoter;

use MrAndMrsSmith\IdempotentConsumerBundle\Message\IncomingMessage;
use MrAndMrsSmith\IdempotentConsumerBundle\ProcessFailedMessageVoter\DefaultProcessFailedMessageVoter;
use PHPUnit\Framework\TestCase;

class DefaultProcessFailedMessageVoterTest extends TestCase
{
    public function testVoteWhenTrue(): void
    {
        $voter = new DefaultProcessFailedMessageVoter(true);
        $this->assertTrue(
            $voter->vote(
                $this->createMock(IncomingMessage::class)
            )
        );
    }


    public function testVoteWhenFalse(): void
    {
        $voter = new DefaultProcessFailedMessageVoter(false);
        $this->assertFalse(
            $voter->vote(
                $this->createMock(IncomingMessage::class)
            )
        );
    }
}
