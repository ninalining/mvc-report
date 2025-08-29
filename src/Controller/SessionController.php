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
                    $deckCards[] = [
                        'label' => method_exists($c, 'getLabel') ? $c->getLabel() : (string)$c,
                        'cssClass' => method_exists($c, 'getCssClass') ? $c->getCssClass() : 'playing-card',
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
