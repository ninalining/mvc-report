<?php

namespace App\Game;

use App\Card\Card;
use App\Card\CardHand;

/**
 * A hand in Black Jack with proper BJ value calculation.
 * A = 1 or 11 (best value), J/Q/K = 10, 2-10 = face value.
 */
class BlackJackHand extends CardHand
{
    /**
     * Calculate the total value of this hand using Black Jack rules.
     * Aces count as 11 unless that would bust, then count as 1.
     */
    public function getValue(): int
    {
        $total = 0;
        $aces = 0;

        foreach ($this->getCards() as $card) {
            $points = $this->cardPoints($card->getValue());
            if ($card->getValue() === 'A') {
                $aces++;
            }
            $total += $points;
        }

        // Reduce aces from 11 to 1 if busting
        while ($total > 21 && $aces > 0) {
            $total -= 10;
            $aces--;
        }

        return $total;
    }

    /**
     * Check if this hand is a natural Black Jack (exactly 2 cards totaling 21).
     */
    public function isBlackJack(): bool
    {
        return $this->getNumberOfCards() === 2 && $this->getValue() === 21;
    }

    /**
     * Check if this hand is busted (over 21).
     */
    public function isBusted(): bool
    {
        return $this->getValue() > 21;
    }

    /**
     * Check if the first two cards can be split (same value).
     */
    public function canSplit(): bool
    {
        if ($this->getNumberOfCards() !== 2) {
            return false;
        }

        $cards = $this->getCards();
        return $this->cardPoints($cards[0]->getValue()) === $this->cardPoints($cards[1]->getValue());
    }

    /**
     * Get the BJ point value of a card face value.
     */
    private function cardPoints(string $value): int
    {
        return match ($value) {
            'A' => 11,
            'K', 'Q', 'J' => 10,
            default => (int) $value,
        };
    }

    /**
     * Return hand data as array for templates/API.
     *
     * @return array<string, mixed>
     */
    public function toArray(bool $hideFirst = false): array
    {
        $cards = [];
        foreach ($this->getCards() as $index => $card) {
            if ($hideFirst && $index === 0) {
                $cards[] = [
                    'label' => '??',
                    'cssClass' => 'bj-card hidden-card',
                    'hidden' => true,
                ];
                continue;
            }

            $cardData = [
                'label' => $card->getValue() . $card->getSuit(),
                'cssClass' => 'bj-card',
                'hidden' => false,
            ];

            if ($card->getSuit() === '♥' || $card->getSuit() === '♦') {
                $cardData['cssClass'] .= ' red';
            }

            $cards[] = $cardData;
        }

        return [
            'cards' => $cards,
            'value' => $hideFirst ? '?' : $this->getValue(),
            'busted' => $this->isBusted(),
            'blackjack' => $this->isBlackJack(),
        ];
    }
}
