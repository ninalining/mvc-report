<?php

namespace App\Game;

/**
 * AI player for Black Jack using basic strategy.
 * Makes decisions based on hand value and dealer's visible card.
 */
class BlackJackAI
{
    /**
     * Decide action for the AI player based on basic strategy.
     *
     * @param BlackJackHand $hand The AI's current hand
     * @param int $dealerUpCard The dealer's visible card value (2-11)
     * @return string 'hit' or 'stand'
     */
    public function decide(BlackJackHand $hand, int $dealerUpCard): string
    {
        $value = $hand->getValue();

        // Always stand on 17+
        if ($value >= 17) {
            return 'stand';
        }

        // Always hit on 8 or less
        if ($value <= 8) {
            return 'hit';
        }

        // Basic strategy for 12-16
        if ($value >= 12) {
            // Stand if dealer shows weak card (2-6), hit if dealer shows strong (7-11)
            if ($dealerUpCard >= 2 && $dealerUpCard <= 6) {
                return 'stand';
            }
            return 'hit';
        }

        // 9-11: always hit (double down in real BJ, but we just hit)
        return 'hit';
    }

    /**
     * Get the point value of the dealer's visible card.
     */
    public function getDealerUpCardValue(BlackJackHand $dealerHand): int
    {
        $cards = $dealerHand->getCards();
        // Second card is visible (first is hidden)
        if (count($cards) < 2) {
            return 0;
        }

        $card = $cards[1];
        $value = $card->getValue();

        return match ($value) {
            'A' => 11,
            'K', 'Q', 'J' => 10,
            default => (int) $value,
        };
    }
}
