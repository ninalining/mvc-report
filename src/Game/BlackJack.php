<?php

namespace App\Game;

use App\Card\DeckOfCards;

/**
 * Black Jack game engine.
 * Handles dealing, hit/stand, dealer logic, and bet settlement.
 * Initially supports single hand; multi-hand and split added later.
 */
class BlackJack
{
    private DeckOfCards $deck;
    private BlackJackHand $dealerHand;
    /** @var BlackJackHand[] Player hands (supports multiple for multi-hand/split) */
    private array $playerHands;
    private int $activeHandIndex;
    private string $playerName;
    private int $balance;
    /** @var int[] Bet for each hand */
    private array $bets;
    /** @var string[] Result per hand: playing|player_bust|dealer_bust|player_win|dealer_win|push|blackjack */
    private array $results;
    private bool $dealerDone;
    private ?BlackJackHand $aiHand;
    private string $aiResult;
    private bool $aiEnabled;

    public function __construct(string $playerName, int $balance, bool $aiEnabled = false)
    {
        $this->playerName = $playerName;
        $this->balance = $balance;
        $this->deck = new DeckOfCards();
        $this->deck->shuffle();
        $this->dealerHand = new BlackJackHand();
        $this->playerHands = [];
        $this->bets = [];
        $this->results = [];
        $this->activeHandIndex = 0;
        $this->dealerDone = false;
        $this->aiHand = null;
        $this->aiResult = '';
        $this->aiEnabled = $aiEnabled;
    }

    /**
     * Deal initial cards for the given number of hands.
     *
     * @param int $numberOfHands Number of player hands (1-3)
     * @param int $betPerHand Bet amount per hand
     */
    public function deal(int $numberOfHands, int $betPerHand): void
    {
        $numberOfHands = max(1, min(3, $numberOfHands));
        $totalBet = $numberOfHands * $betPerHand;

        if ($totalBet > $this->balance) {
            return;
        }

        $this->balance -= $totalBet;
        $this->playerHands = [];
        $this->bets = [];
        $this->results = [];
        $this->activeHandIndex = 0;
        $this->dealerDone = false;
        $this->dealerHand = new BlackJackHand();
        $this->aiHand = $this->aiEnabled ? new BlackJackHand() : null;
        $this->aiResult = '';

        for ($i = 0; $i < $numberOfHands; $i++) {
            $this->playerHands[] = new BlackJackHand();
            $this->bets[] = $betPerHand;
            $this->results[] = 'playing';
        }

        // Deal 2 cards to each hand and dealer
        for ($round = 0; $round < 2; $round++) {
            foreach ($this->playerHands as $hand) {
                $card = $this->deck->drawCard();
                if ($card !== null) {
                    $hand->addCard($card);
                }
            }
            // Deal to AI if enabled
            if ($this->aiEnabled && $this->aiHand !== null) {
                $card = $this->deck->drawCard();
                if ($card !== null) {
                    $this->aiHand->addCard($card);
                }
            }
            $card = $this->deck->drawCard();
            if ($card !== null) {
                $this->dealerHand->addCard($card);
            }
        }

        // Check for immediate blackjacks
        foreach ($this->playerHands as $i => $hand) {
            if ($hand->isBlackJack()) {
                $this->results[$i] = 'blackjack';
            }
        }

        // If all hands are blackjack, run dealer
        if ($this->allHandsDone()) {
            $this->dealerPlay();
        }

        // Skip to first playable hand
        $this->advanceToNextPlayableHand();
    }

    /**
     * Player hits on the currently active hand.
     */
    public function hit(): void
    {
        if ($this->isGameOver()) {
            return;
        }

        $hand = $this->getActiveHand();
        if ($hand === null) {
            return;
        }

        $card = $this->deck->drawCard();
        if ($card !== null) {
            $hand->addCard($card);
        }

        if ($hand->isBusted()) {
            $this->results[$this->activeHandIndex] = 'player_bust';
            $this->advanceToNextPlayableHand();
        }
    }

    /**
     * Player stands on the currently active hand.
     */
    public function stand(): void
    {
        if ($this->isGameOver()) {
            return;
        }

        $this->advanceToNextPlayableHand(true);
    }

    /**
     * Split the active hand into two separate hands.
     * Requires exactly 2 cards of equal value and sufficient balance.
     */
    public function split(): void
    {
        if ($this->isGameOver()) {
            return;
        }

        $hand = $this->getActiveHand();
        if ($hand === null || !$hand->canSplit()) {
            return;
        }

        $bet = $this->bets[$this->activeHandIndex];
        if ($bet > $this->balance) {
            return;
        }

        // Deduct additional bet for the new hand
        $this->balance -= $bet;

        // Take the second card from the current hand
        $cards = $hand->getCards();
        $secondCard = $cards[1];

        // Create new hand with the second card
        $newHand = new BlackJackHand();
        $newHand->addCard($secondCard);

        // Rebuild original hand with only the first card
        $firstCard = $cards[0];
        $this->playerHands[$this->activeHandIndex] = new BlackJackHand();
        $this->playerHands[$this->activeHandIndex]->addCard($firstCard);

        // Draw a new card for each hand
        $card1 = $this->deck->drawCard();
        if ($card1 !== null) {
            $this->playerHands[$this->activeHandIndex]->addCard($card1);
        }

        $card2 = $this->deck->drawCard();
        if ($card2 !== null) {
            $newHand->addCard($card2);
        }

        // Insert the new hand right after the current one
        $insertAt = $this->activeHandIndex + 1;
        array_splice($this->playerHands, $insertAt, 0, [$newHand]);
        array_splice($this->bets, $insertAt, 0, [$bet]);
        array_splice($this->results, $insertAt, 0, ['playing']);

        // Check if either new hand is blackjack
        if ($this->playerHands[$this->activeHandIndex]->isBlackJack()) {
            $this->results[$this->activeHandIndex] = 'blackjack';
            $this->advanceToNextPlayableHand();
        }
    }

    /**
     * Move to the next hand that is still playing.
     * If no more hands, trigger dealer play.
     */
    private function advanceToNextPlayableHand(bool $skipCurrent = false): void
    {
        $start = $skipCurrent ? $this->activeHandIndex + 1 : $this->activeHandIndex;
        $handCount = count($this->playerHands);

        for ($i = $start; $i < $handCount; $i++) {
            if ($this->results[$i] === 'playing') {
                $this->activeHandIndex = $i;
                return;
            }
        }

        // All hands done, dealer plays
        if (!$this->dealerDone) {
            $this->dealerPlay();
        }
    }

    /**
     * Dealer draws until >= 17, then settle all hands.
     */
    private function dealerPlay(): void
    {
        $this->dealerDone = true;

        // AI plays before dealer using basic strategy
        $this->playAI();

        // Check if all hands are busted — dealer doesn't need to draw
        $allBusted = true;
        foreach ($this->results as $result) {
            if ($result === 'playing' || $result === 'blackjack') {
                $allBusted = false;
                break;
            }
        }

        // Also check AI hand
        if ($this->aiHand !== null && !$this->aiHand->isBusted()) {
            $allBusted = false;
        }

        if (!$allBusted) {
            while ($this->dealerHand->getValue() < 17) {
                $card = $this->deck->drawCard();
                if ($card === null) {
                    break;
                }
                $this->dealerHand->addCard($card);
            }
        }

        $this->settleAllHands();
    }

    /**
     * AI player plays using basic strategy before the dealer.
     */
    private function playAI(): void
    {
        if ($this->aiHand === null) {
            return;
        }

        if ($this->aiHand->isBlackJack()) {
            $this->aiResult = 'blackjack';
            return;
        }

        $ai = new BlackJackAI();
        $dealerUpCard = $ai->getDealerUpCardValue($this->dealerHand);

        // AI plays until it stands or busts
        while (!$this->aiHand->isBusted()) {
            $action = $ai->decide($this->aiHand, $dealerUpCard);
            if ($action === 'stand') {
                break;
            }
            $card = $this->deck->drawCard();
            if ($card === null) {
                break;
            }
            $this->aiHand->addCard($card);
        }

        if ($this->aiHand->isBusted()) {
            $this->aiResult = 'player_bust';
        }
    }

    /**
     * Determine the result for each hand and update balance.
     */
    private function settleAllHands(): void
    {
        $dealerValue = $this->dealerHand->getValue();
        $dealerBusted = $this->dealerHand->isBusted();
        $dealerBlackJack = $this->dealerHand->isBlackJack();

        foreach ($this->playerHands as $i => $hand) {
            if ($this->results[$i] === 'player_bust') {
                // Already lost, bet already deducted
                continue;
            }

            $bet = $this->bets[$i];

            if ($this->results[$i] === 'blackjack') {
                if ($dealerBlackJack) {
                    // Both blackjack: push
                    $this->results[$i] = 'push';
                    $this->balance += $bet;
                } else {
                    // Player blackjack: 3:2 payout
                    $this->results[$i] = 'blackjack';
                    $this->balance += $bet + (int) floor($bet * 1.5);
                }
                continue;
            }

            if ($dealerBusted) {
                $this->results[$i] = 'dealer_bust';
                $this->balance += $bet * 2;
                continue;
            }

            $playerValue = $hand->getValue();

            if ($playerValue > $dealerValue) {
                $this->results[$i] = 'player_win';
                $this->balance += $bet * 2;
            } elseif ($playerValue === $dealerValue) {
                $this->results[$i] = 'push';
                $this->balance += $bet;
            } else {
                $this->results[$i] = 'dealer_win';
                // Bet already deducted
            }
        }

        // Settle AI hand
        $this->settleAIHand($dealerValue, $dealerBusted, $dealerBlackJack);
    }

    /**
     * Determine AI result (no money involved, just for display).
     */
    private function settleAIHand(int $dealerValue, bool $dealerBusted, bool $dealerBlackJack): void
    {
        if ($this->aiHand === null || $this->aiResult === 'player_bust') {
            return;
        }

        if ($this->aiResult === 'blackjack') {
            if ($dealerBlackJack) {
                $this->aiResult = 'push';
            }
            return;
        }

        if ($dealerBusted) {
            $this->aiResult = 'dealer_bust';
            return;
        }

        $aiValue = $this->aiHand->getValue();
        if ($aiValue > $dealerValue) {
            $this->aiResult = 'player_win';
        } elseif ($aiValue === $dealerValue) {
            $this->aiResult = 'push';
        } else {
            $this->aiResult = 'dealer_win';
        }
    }

    /**
     * Check if all hands are resolved and dealer is done.
     */
    public function isGameOver(): bool
    {
        return $this->dealerDone;
    }

    /**
     * Check if all player hands are done (busted or blackjack).
     */
    private function allHandsDone(): bool
    {
        foreach ($this->results as $result) {
            if ($result === 'playing') {
                return false;
            }
        }
        return true;
    }

    public function getActiveHand(): ?BlackJackHand
    {
        return $this->playerHands[$this->activeHandIndex] ?? null;
    }

    public function getActiveHandIndex(): int
    {
        return $this->activeHandIndex;
    }

    public function getDealerHand(): BlackJackHand
    {
        return $this->dealerHand;
    }

    /**
     * @return BlackJackHand[]
     */
    public function getPlayerHands(): array
    {
        return $this->playerHands;
    }

    /**
     * @return string[]
     */
    public function getResults(): array
    {
        return $this->results;
    }

    /**
     * @return int[]
     */
    public function getBets(): array
    {
        return $this->bets;
    }

    public function getPlayerName(): string
    {
        return $this->playerName;
    }

    public function getBalance(): int
    {
        return $this->balance;
    }

    public function setBalance(int $balance): void
    {
        $this->balance = $balance;
    }

    public function getDeck(): DeckOfCards
    {
        return $this->deck;
    }

    public function getAiHand(): ?BlackJackHand
    {
        return $this->aiHand;
    }

    public function getAiResult(): string
    {
        return $this->aiResult;
    }

    public function isAiEnabled(): bool
    {
        return $this->aiEnabled;
    }

    /**
     * Get full game state as array for templates/API.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $hands = [];
        foreach ($this->playerHands as $i => $hand) {
            $isActive = ($i === $this->activeHandIndex && !$this->isGameOver());
            $hands[] = [
                'hand' => $hand->toArray(),
                'bet' => $this->bets[$i],
                'result' => $this->results[$i],
                'active' => $isActive,
                'canSplit' => $isActive && $hand->canSplit() && $this->bets[$i] <= $this->balance,
            ];
        }

        return [
            'playerName' => $this->playerName,
            'balance' => $this->balance,
            'dealer' => $this->dealerHand->toArray(!$this->isGameOver()),
            'hands' => $hands,
            'gameOver' => $this->isGameOver(),
            'activeHandIndex' => $this->activeHandIndex,
            'aiEnabled' => $this->aiEnabled,
            'ai' => $this->aiHand ? $this->aiHand->toArray(!$this->isGameOver()) : null,
            'aiResult' => $this->aiResult,
        ];
    }

    /**
     * Get a result message for a given hand.
     */
    public function getResultMessage(int $handIndex): string
    {
        $result = $this->results[$handIndex] ?? 'playing';

        return match ($result) {
            'player_bust' => 'Tjock! Du förlorade.',
            'dealer_bust' => 'Banken är tjock! Du vinner!',
            'player_win' => 'Du vinner!',
            'dealer_win' => 'Banken vinner.',
            'push' => 'Lika — insatsen returneras.',
            'blackjack' => 'Black Jack! Du vinner 3:2!',
            default => 'Spelet pågår...',
        };
    }
}
