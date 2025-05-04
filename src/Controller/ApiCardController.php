<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Card\DeckOfCards;

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
}
