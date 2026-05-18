<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Card\DeckOfCards;
use App\Game\Game21;

class ApiCardController extends AbstractController
{
    #[Route('/api/deck', name: 'api_deck', methods: ['GET'])]
    public function getDeck(SessionInterface $session): JsonResponse
    {
        $deck = $session->get('deck', new DeckOfCards());
        $session->set('deck', $deck);

        return $this->json([
            'deck' => array_map(fn ($card) => (string) $card, $deck->getCards()),
        ]);
    }

    #[Route('/api/deck/shuffle', name: 'api_deck_shuffle', methods: ['POST'])]
    public function shuffleDeck(SessionInterface $session): JsonResponse
    {
        $deck = new DeckOfCards();
        $deck->shuffle();
        $session->set('deck', $deck);

        return $this->json([
            'message' => 'Deck shuffled successfully!',
            'deck' => array_map(fn ($card) => (string) $card, $deck->getCards()),
        ]);
    }

    #[Route('/api/deck/draw', name: 'api_deck_draw', methods: ['POST'])]
    public function drawCard(SessionInterface $session): JsonResponse
    {
        $deck = $session->get('deck', new DeckOfCards());
        $card = $deck->drawCard();
        $session->set('deck', $deck);

        return $this->json([
            'card' => $card ? (string) $card : null,
            'remaining' => $deck->getNumberOfCards(),
        ]);
    }

    #[Route('/api/deck/draw/{number<\d+>}', name: 'api_deck_draw_number', methods: ['POST'])]
    public function drawCards(SessionInterface $session, int $number): JsonResponse
    {
        $deck = $session->get('deck', new DeckOfCards());
        $cards = $deck->drawCards($number);
        $session->set('deck', $deck);

        return $this->json([
            'cards' => array_map(fn ($card) => (string) $card, $cards),
            'remaining' => $deck->getNumberOfCards(),
        ]);
    }

    #[Route('/api/game', name: 'api_game', methods: ['GET'])]
    public function getGame(SessionInterface $session): JsonResponse
    {
        $game = $session->get('game21');

        if (!$game instanceof Game21) {
            return $this->json([
                'message' => 'Inget spel har startats.',
            ]);
        }

        return $this->json([
            'player' => [
                'hand' => array_map(fn ($card) => (string) $card, $game->getPlayerHand()->getCards()),
                'value' => $game->getHandValue($game->getPlayerHand()),
            ],
            'dealer' => [
                'hand' => array_map(fn ($card) => (string) $card, $game->getDealerHand()->getCards()),
                'value' => $game->getHandValue($game->getDealerHand()),
            ],
            'status' => $game->getStatus(),
            'message' => $game->getStatusMessage(),
            'gameOver' => $game->isGameOver(),
        ]);
    }
}
