<?php

namespace App\Entity;

use App\Repository\GameRoundRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GameRoundRepository::class)]
class GameRound
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Player::class, inversedBy: 'gameRounds')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Player $player = null;

    #[ORM\Column]
    private int $bet = 0;

    #[ORM\Column(length: 20)]
    private string $result = '';

    #[ORM\Column]
    private int $playerScore = 0;

    #[ORM\Column]
    private int $dealerScore = 0;

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPlayer(): ?Player
    {
        return $this->player;
    }

    public function setPlayer(Player $player): static
    {
        $this->player = $player;
        return $this;
    }

    public function getBet(): int
    {
        return $this->bet;
    }

    public function setBet(int $bet): static
    {
        $this->bet = $bet;
        return $this;
    }

    public function getResult(): string
    {
        return $this->result;
    }

    public function setResult(string $result): static
    {
        $this->result = $result;
        return $this;
    }

    public function getPlayerScore(): int
    {
        return $this->playerScore;
    }

    public function setPlayerScore(int $playerScore): static
    {
        $this->playerScore = $playerScore;
        return $this;
    }

    public function getDealerScore(): int
    {
        return $this->dealerScore;
    }

    public function setDealerScore(int $dealerScore): static
    {
        $this->dealerScore = $dealerScore;
        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }
}
