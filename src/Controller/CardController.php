<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Card\DeckOfCards;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CardController extends AbstractController
{
    #[Route('/card', name: 'card_index')]
    public function index(): Response
    {
        return $this->render('card.html.twig');
    }

    #[Route('/card/deck', name: 'card_deck')]
    public function deck(SessionInterface $session): Response
    {
        $deck = $session->get('deck');

        if (!$deck) {
            $deck = new DeckOfCards();
            $session->set('deck', $deck);
        }

        $deck->sort();
        $groupedCards = $deck->getGroupedCards();

        return $this->render('card/deck.html.twig', [
            'cards' => $groupedCards,
        ]);
    }

    #[Route('/card/deck/shuffle', name: 'card_shuffle')]
    public function shuffle(SessionInterface $session): Response
    {
        $deck = new DeckOfCards();
        $deck->shuffle();
        $session->set('deck', $deck);
        $session->set('example_key', 'example_value');
        $cards = $deck->getCards();

        return $this->render('card/shuffle.html.twig', [
            'cards' => $cards,
        ]);
    }

    #[Route('/card/deck/draw', name: 'card_draw')]
    public function draw(SessionInterface $session): Response
    {
        $deck = $session->get('deck');

        if (!$deck) {
            $deck = new DeckOfCards();
        }

        $cardObj = $deck->drawCard();
        $session->set('deck', $deck);

        $card = null;
        if ($cardObj && method_exists($cardObj, 'toArray')) {
            $card = $cardObj->toArray();
        } elseif ($cardObj) {
            $card = [
                'label' => (string)$cardObj,
                'cssClass' => 'playing-card',
                'suit' => $cardObj->getSuit(),
                'value' => $cardObj->getValue(),
            ];
        }

        return $this->render('card/draw.html.twig', [
            'card' => $card,
            'remaining' => $deck->getNumberOfCards(),
        ]);
    }

    #[Route('/card/deck/draw/{number}', name: 'card_draw_number')]
    public function drawNumber(SessionInterface $session, int $number): Response
    {
        $deck = $session->get('deck');

        if (!$deck) {
            $deck = new DeckOfCards();
        }

        $cardsObj = $deck->drawCards($number);
        $session->set('deck', $deck);
        $cards = array_map(function($c) {
            return method_exists($c, 'toArray') ? $c->toArray() : [
                'label' => (string)$c,
                'cssClass' => 'playing-card',
                'suit' => $c->getSuit(),
                'value' => $c->getValue(),
            ];
        }, $cardsObj);

        return $this->render('card/draw_number.html.twig', [
            'cards' => $cards,
            'remaining' => $deck->getNumberOfCards(),
        ]);
    }
}
