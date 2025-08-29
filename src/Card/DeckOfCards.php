<?php

namespace App\Card;

class DeckOfCards
{
    private array $deck = [];
    private bool $shuffled = false;

    private static array $suits = ['♠', '♥', '♦', '♣'];
    private static array $values = ['2', '3', '4', '5', '6', '7', '8', '9', '10', 'J', 'Q', 'K', 'A'];

    public function __construct()
    {
        foreach (self::$suits as $suit) {
            foreach (self::$values as $value) {
                $this->deck[] = new CardGraphic($suit, $value);
            }
        }
    }

    public function shuffle(): void
    {
        shuffle($this->deck);
        $this->shuffled = true;
    }

    public function isShuffled(): bool
    {
        return $this->shuffled;
    }

    public function drawCard(): ?Card
    {
        return array_shift($this->deck);
    }

    public function drawCards(int $number): array
    {
        $drawn = [];
        for ($i = 0; $i < $number; $i++) {
            $card = $this->drawCard();
            if ($card !== null) {
                $drawn[] = $card;
            }
        }
        return $drawn;
    }

    public function getCards(): array
    {
        return $this->deck;
    }

    public function getNumberOfCards(): int
    {
        return count($this->deck);
    }
    public function sort(): void
    {
        usort($this->deck, function (Card $a, Card $b) {
            $suitOrder = array_flip(self::$suits);
            $valueOrder = array_flip(self::$values);

            if ($suitOrder[$a->getSuit()] === $suitOrder[$b->getSuit()]) {
                return $valueOrder[$a->getValue()] <=> $valueOrder[$b->getValue()];
            }

            return $suitOrder[$a->getSuit()] <=> $suitOrder[$b->getSuit()];
        });
    }

    public function getGroupedCards(): array
    {
        $grouped = [];
        foreach ($this->deck as $card) {
            if (method_exists($card, 'toArray')) {
                $grouped[$card->getSuit()][] = $card->toArray();
            } else {
                $grouped[$card->getSuit()][] = [
                    'label' => (string)$card,
                    'cssClass' => 'playing-card',
                    'suit' => $card->getSuit(),
                    'value' => $card->getValue(),
                ];
            }
        }
        return $grouped;
    }
}
