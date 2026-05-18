<?php

namespace App\Game;

use App\Card\CardHand;
use App\Card\DeckOfCards;

/**
 * Game21 - Kortspelet 21 (Swedish card game "21").
 *
 * Rules: Player and dealer each get one card. Player can Hit or Stand.
 * Face cards: J=11, Q=12, K=13, A=1 or 14. Bust over 21.
 * Dealer draws until >= 17. Dealer wins on tie.
 */
class Game21
{
    /** @var DeckOfCards The deck used in the game */
    private DeckOfCards $deck;

    /** @var CardHand The player's hand */
    private CardHand $player;

    /** @var CardHand The dealer's hand */
    private CardHand $dealer;

    /** @var string Current game status: playing|player_bust|dealer_bust|player_win|dealer_win */
    private string $status;

    /**
     * Create a new game, shuffle deck, and deal one card to each.
     */
    public function __construct()
    {
        $this->deck = new DeckOfCards();
        $this->deck->shuffle();
        $this->player = new CardHand();
        $this->dealer = new CardHand();
        $this->status = 'playing';

        $this->player->addCard($this->deck->drawCard());
        $this->dealer->addCard($this->deck->drawCard());
    }

    /**
     * Player draws one card. Sets status to player_bust if over 21.
     */
    public function playerHit(): void
    {
        if ($this->status !== 'playing') {
            return;
        }

        $card = $this->deck->drawCard();
        if ($card !== null) {
            $this->player->addCard($card);
        }

        if ($this->getHandValue($this->player) > 21) {
            $this->status = 'player_bust';
        }
    }

    /**
     * Player stands; dealer plays automatically.
     */
    public function playerStand(): void
    {
        if ($this->status !== 'playing') {
            return;
        }

        $this->dealerPlay();
    }

    /**
     * Dealer draws cards until hand value >= 17.
     */
    private function dealerPlay(): void
    {
        while ($this->getHandValue($this->dealer) < 17) {
            $card = $this->deck->drawCard();
            if ($card === null) {
                break;
            }
            $this->dealer->addCard($card);
        }

        if ($this->getHandValue($this->dealer) > 21) {
            $this->status = 'dealer_bust';
            return;
        }

        $this->compareHands();
    }

    /**
     * Compare player and dealer hands. Dealer wins on tie.
     */
    private function compareHands(): void
    {
        $playerValue = $this->getHandValue($this->player);
        $dealerValue = $this->getHandValue($this->dealer);

        if ($dealerValue >= $playerValue) {
            $this->status = 'dealer_win';
            return;
        }

        $this->status = 'player_win';
    }

    /**
     * Calculate the value of a hand. Ace = 14, reduced to 1 if bust.
     *
     * @param CardHand $hand The hand to evaluate
     * @return int Total hand value
     */
    public function getHandValue(CardHand $hand): int
    {
        $total = 0;
        $aces = 0;

        foreach ($hand->getCards() as $card) {
            $value = $card->getValue();
            $points = $this->cardPoints($value);

            if ($value === 'A') {
                $aces++;
            }

            $total += $points;
        }

        while ($total > 21 && $aces > 0) {
            $total -= 13; // 14 -> 1
            $aces--;
        }

        return $total;
    }

    /**
     * Get point value for a card face value.
     */
    private function cardPoints(string $value): int
    {
        return match (true) {
            $value === 'A' => 14,
            $value === 'K' => 13,
            $value === 'Q' => 12,
            $value === 'J' => 11,
            default => (int) $value,
        };
    }

    /**
     * Get current game status string.
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * Get the player's hand.
     */
    public function getPlayerHand(): CardHand
    {
        return $this->player;
    }

    /**
     * Get the dealer's hand.
     */
    public function getDealerHand(): CardHand
    {
        return $this->dealer;
    }

    /**
     * Check if game is over.
     */
    public function isGameOver(): bool
    {
        return $this->status !== 'playing';
    }

    /**
     * Get a Swedish status message for the current game state.
     */
    public function getStatusMessage(): string
    {
        return match ($this->status) {
            'player_bust' => 'Spelaren är tjock! Banken vinner.',
            'dealer_bust' => 'Banken är tjock! Spelaren vinner.',
            'player_win' => 'Spelaren vinner!',
            'dealer_win' => 'Banken vinner!',
            default => 'Spelet pågår...',
        };
    }
}
