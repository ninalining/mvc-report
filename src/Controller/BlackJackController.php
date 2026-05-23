<?php

namespace App\Controller;

use App\Entity\GameRound;
use App\Entity\Player;
use App\Game\BlackJack;
use App\Repository\PlayerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class BlackJackController extends AbstractController
{
    #[Route('/proj/blackjack', name: 'proj_blackjack_login')]
    public function login(): Response
    {
        return $this->render('proj/blackjack/login.html.twig');
    }

    #[Route('/proj/blackjack/login', name: 'proj_blackjack_login_post', methods: ['POST'])]
    public function loginPost(
        Request $request,
        SessionInterface $session,
        PlayerRepository $playerRepo,
        EntityManagerInterface $em
    ): Response {
        $playerName = trim((string) $request->request->get('playerName', ''));

        if ($playerName === '') {
            $this->addFlash('error', 'Ange ett spelarnamn.');
            return $this->redirectToRoute('proj_blackjack_login');
        }

        // Find or create player in database
        $player = $playerRepo->findByName($playerName);
        if (!$player) {
            $player = new Player();
            $player->setName($playerName);
            $em->persist($player);
            $em->flush();
        }

        $session->set('bj_player_name', $playerName);

        return $this->redirectToRoute('proj_blackjack_bet');
    }

    #[Route('/proj/blackjack/bet', name: 'proj_blackjack_bet')]
    public function bet(SessionInterface $session, PlayerRepository $playerRepo): Response
    {
        $playerName = $session->get('bj_player_name');
        if (!$playerName) {
            return $this->redirectToRoute('proj_blackjack_login');
        }

        $player = $playerRepo->findByName($playerName);
        $balance = $player ? $player->getBalance() : 1000;

        return $this->render('proj/blackjack/bet.html.twig', [
            'playerName' => $playerName,
            'balance' => $balance,
        ]);
    }

    #[Route('/proj/blackjack/deal', name: 'proj_blackjack_deal', methods: ['POST'])]
    public function deal(
        Request $request,
        SessionInterface $session,
        PlayerRepository $playerRepo
    ): Response {
        $playerName = $session->get('bj_player_name');
        if (!$playerName) {
            return $this->redirectToRoute('proj_blackjack_login');
        }

        $player = $playerRepo->findByName($playerName);
        $balance = $player ? $player->getBalance() : 1000;
        $bet = (int) $request->request->get('bet', 10);
        $hands = (int) $request->request->get('hands', 1);

        $hands = max(1, min(3, $hands));
        $bet = max(1, $bet);

        if ($bet * $hands > $balance) {
            $this->addFlash('error', 'Du har inte tillräckligt med pengar.');
            return $this->redirectToRoute('proj_blackjack_bet');
        }

        $aiEnabled = (bool) $request->request->get('aiEnabled', false);
        $game = new BlackJack($playerName, $balance, $aiEnabled);
        $game->deal($hands, $bet);

        $session->set('bj_game', $game);

        return $this->redirectToRoute('proj_blackjack_play');
    }

    #[Route('/proj/blackjack/play', name: 'proj_blackjack_play')]
    public function play(SessionInterface $session): Response
    {
        $game = $session->get('bj_game');
        if (!$game instanceof BlackJack) {
            return $this->redirectToRoute('proj_blackjack_login');
        }

        $messages = [];
        foreach ($game->getPlayerHands() as $i => $hand) {
            $messages[] = $game->getResultMessage($i);
        }

        return $this->render('proj/blackjack/play.html.twig', [
            'game' => $game->toArray(),
            'messages' => $messages,
        ]);
    }

    #[Route('/proj/blackjack/hit', name: 'proj_blackjack_hit', methods: ['POST'])]
    public function hit(
        SessionInterface $session,
        PlayerRepository $playerRepo,
        EntityManagerInterface $em
    ): Response {
        $game = $session->get('bj_game');
        if (!$game instanceof BlackJack) {
            return $this->redirectToRoute('proj_blackjack_login');
        }

        $game->hit();
        $session->set('bj_game', $game);

        if ($game->isGameOver()) {
            $this->saveGameResults($game, $playerRepo, $em);
        }

        return $this->redirectToRoute('proj_blackjack_play');
    }

    #[Route('/proj/blackjack/stand', name: 'proj_blackjack_stand', methods: ['POST'])]
    public function stand(
        SessionInterface $session,
        PlayerRepository $playerRepo,
        EntityManagerInterface $em
    ): Response {
        $game = $session->get('bj_game');
        if (!$game instanceof BlackJack) {
            return $this->redirectToRoute('proj_blackjack_login');
        }

        $game->stand();
        $session->set('bj_game', $game);

        if ($game->isGameOver()) {
            $this->saveGameResults($game, $playerRepo, $em);
        }

        return $this->redirectToRoute('proj_blackjack_play');
    }

    #[Route('/proj/blackjack/split', name: 'proj_blackjack_split', methods: ['POST'])]
    public function split(
        SessionInterface $session,
        PlayerRepository $playerRepo,
        EntityManagerInterface $em
    ): Response {
        $game = $session->get('bj_game');
        if (!$game instanceof BlackJack) {
            return $this->redirectToRoute('proj_blackjack_login');
        }

        $game->split();
        $session->set('bj_game', $game);

        if ($game->isGameOver()) {
            $this->saveGameResults($game, $playerRepo, $em);
        }

        return $this->redirectToRoute('proj_blackjack_play');
    }

    /**
     * Save game results to the database when a round finishes.
     */
    private function saveGameResults(
        BlackJack $game,
        PlayerRepository $playerRepo,
        EntityManagerInterface $em
    ): void {
        $player = $playerRepo->findByName($game->getPlayerName());
        if (!$player) {
            return;
        }

        $player->setBalance($game->getBalance());

        $dealerScore = $game->getDealerHand()->getValue();

        foreach ($game->getPlayerHands() as $i => $hand) {
            $round = new GameRound();
            $round->setPlayer($player);
            $round->setBet($game->getBets()[$i]);
            $round->setResult($game->getResults()[$i]);
            $round->setPlayerScore($hand->getValue());
            $round->setDealerScore($dealerScore);
            $em->persist($round);
        }

        $em->flush();
    }
}
