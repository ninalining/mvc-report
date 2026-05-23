<?php

namespace App\Tests\Entity;

use App\Entity\Player;
use App\Entity\GameRound;
use PHPUnit\Framework\TestCase;

class PlayerTest extends TestCase
{
    public function testDefaultValues(): void
    {
        $player = new Player();
        $this->assertNull($player->getId());
        $this->assertNull($player->getName());
        $this->assertSame(1000, $player->getBalance());
        $this->assertCount(0, $player->getGameRounds());
    }

    public function testSetName(): void
    {
        $player = new Player();
        $result = $player->setName('Alice');
        $this->assertSame('Alice', $player->getName());
        $this->assertSame($player, $result);
    }

    public function testSetBalance(): void
    {
        $player = new Player();
        $result = $player->setBalance(500);
        $this->assertSame(500, $player->getBalance());
        $this->assertSame($player, $result);
    }
}
