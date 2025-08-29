<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class SessionController extends AbstractController
{
    #[Route('/session', name: 'session_index')]
    public function index(SessionInterface $session): Response
    {
        $sessionData = $session->all();
        //dump($session->all());

            $deck = $session->get('deck');
            $deckCards = [];
            $deckCount = 0;
            $shuffled = false;
            if ($deck instanceof \App\Card\DeckOfCards) {
                foreach ($deck->getCards() as $c) {
                    $deckCards[] = method_exists($c, 'toArray') ? $c->toArray() : [
                        'label' => (string)$c,
                        'cssClass' => 'playing-card',
                        'suit' => $c->getSuit(),
                        'value' => $c->getValue(),
                    ];
                }
                $deckCount = $deck->getNumberOfCards();
                $shuffled = method_exists($deck, 'isShuffled') ? $deck->isShuffled() : false;
            }

            return $this->render('session/index.html.twig', [
                'sessionData' => $sessionData,
                'deckCards' => $deckCards,
                'deckCount' => $deckCount,
                'shuffled' => $shuffled,
            ]);
    }

    #[Route('/session/delete', name: 'session_delete')]
    public function delete(SessionInterface $session): Response
    {
        $session->clear();
        $this->addFlash('notice', 'Sessionen har raderats!');

        return $this->redirectToRoute('session_index');
    }
}
