<?php

namespace App\Tests\Entity;

use App\Entity\GameRound;
use App\Entity\Player;
use PHPUnit\Framework\TestCase;

class GameRoundTest extends TestCase
{
    public function testDefaultValues(): void
    {
        $round = new GameRound();
        $this->assertNull($round->getId());
        $this->assertNull($round->getPlayer());
        $this->assertSame(0, $round->getBet());
        $this->assertSame('', $round->getResult());
        $this->assertSame(0, $round->getPlayerScore());
        $this->assertSame(0, $round->getDealerScore());
        $this->assertInstanceOf(\DateTimeImmutable::class, $round->getCreatedAt());
    }

    public function testSetPlayer(): void
    {
        $player = new Player();
        $player->setName('Bob');

        $round = new GameRound();
        $result = $round->setPlayer($player);
        $this->assertSame($player, $round->getPlayer());
        $this->assertSame($round, $result);
    }

    public function testSetBet(): void
    {
        $round = new GameRound();
        $result = $round->setBet(50);
        $this->assertSame(50, $round->getBet());
        $this->assertSame($round, $result);
    }

    public function testSetResult(): void
    {
        $round = new GameRound();
        $result = $round->setResult('player_win');
        $this->assertSame('player_win', $round->getResult());
        $this->assertSame($round, $result);
    }

    public function testSetPlayerScore(): void
    {
        $round = new GameRound();
        $result = $round->setPlayerScore(20);
        $this->assertSame(20, $round->getPlayerScore());
        $this->assertSame($round, $result);
    }

    public function testSetDealerScore(): void
    {
        $round = new GameRound();
        $result = $round->setDealerScore(18);
        $this->assertSame(18, $round->getDealerScore());
        $this->assertSame($round, $result);
    }
}
