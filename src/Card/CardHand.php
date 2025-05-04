<?php

namespace App\Card;

class CardHand
{
    /** @var Card[] */
    private array $cards = [];

    public function addCard(Card $card): void
    {
        $this->cards[] = $card;
    }

    /**
     * @return Card[]
     */
    public function getCards(): array
    {
        return $this->cards;
    }

    public function getNumberOfCards(): int
    {
        return count($this->cards);
    }

    public function __toString(): string
    {
        $strings = array_map(fn ($card) => (string) $card, $this->cards);
        return implode(', ', $strings);
    }
}
