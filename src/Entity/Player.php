<?php

namespace App\Entity;

use App\Repository\PlayerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PlayerRepository::class)]
class Player
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50, unique: true)]
    private ?string $name = null;

    #[ORM\Column]
    private int $balance = 1000;

    /** @var Collection<int, GameRound> */
    #[ORM\OneToMany(targetEntity: GameRound::class, mappedBy: 'player')]
    private Collection $gameRounds;

    public function __construct()
    {
        $this->gameRounds = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    public function getBalance(): int
    {
        return $this->balance;
    }

    public function setBalance(int $balance): static
    {
        $this->balance = $balance;
        return $this;
    }

    /**
     * @return Collection<int, GameRound>
     */
    public function getGameRounds(): Collection
    {
        return $this->gameRounds;
    }
}
