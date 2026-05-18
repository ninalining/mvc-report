<?php

namespace App\Controller;

use App\Game\Game21;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class GameController extends AbstractController
{
    #[Route('/game', name: 'game_index')]
    public function index(): Response
    {
        return $this->render('game/index.html.twig');
    }

    #[Route('/game/doc', name: 'game_doc')]
    public function doc(): Response
    {
        return $this->render('game/doc.html.twig');
    }

    #[Route('/game/start', name: 'game_start', methods: ['GET', 'POST'])]
    public function start(SessionInterface $session): Response
    {
        $game = new Game21();
        $session->set('game21', $game);

        return $this->redirectToRoute('game_play');
    }

    #[Route('/game/play', name: 'game_play')]
    public function play(SessionInterface $session): Response
    {
        $game = $session->get('game21');

        if (!$game instanceof Game21) {
            return $this->redirectToRoute('game_start');
        }

        return $this->render('game/play.html.twig', [
            'playerHand' => $game->getPlayerHand(),
            'dealerHand' => $game->getDealerHand(),
            'playerValue' => $game->getHandValue($game->getPlayerHand()),
            'dealerValue' => $game->getHandValue($game->getDealerHand()),
            'gameOver' => $game->isGameOver(),
            'status' => $game->getStatus(),
            'message' => $game->getStatusMessage(),
        ]);
    }

    #[Route('/game/hit', name: 'game_hit', methods: ['POST'])]
    public function hit(SessionInterface $session): Response
    {
        $game = $session->get('game21');

        if (!$game instanceof Game21) {
            return $this->redirectToRoute('game_start');
        }

        $game->playerHit();
        $session->set('game21', $game);

        if ($game->isGameOver()) {
            return $this->redirectToRoute('game_result');
        }

        return $this->redirectToRoute('game_play');
    }

    #[Route('/game/stand', name: 'game_stand', methods: ['POST'])]
    public function stand(SessionInterface $session): Response
    {
        $game = $session->get('game21');

        if (!$game instanceof Game21) {
            return $this->redirectToRoute('game_start');
        }

        $game->playerStand();
        $session->set('game21', $game);

        return $this->redirectToRoute('game_result');
    }

    #[Route('/game/result', name: 'game_result')]
    public function result(SessionInterface $session): Response
    {
        $game = $session->get('game21');

        if (!$game instanceof Game21) {
            return $this->redirectToRoute('game_start');
        }

        return $this->render('game/result.html.twig', [
            'playerHand' => $game->getPlayerHand(),
            'dealerHand' => $game->getDealerHand(),
            'playerValue' => $game->getHandValue($game->getPlayerHand()),
            'dealerValue' => $game->getHandValue($game->getDealerHand()),
            'message' => $game->getStatusMessage(),
            'status' => $game->getStatus(),
        ]);
    }
}
