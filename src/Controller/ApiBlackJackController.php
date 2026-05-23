<?php

namespace App\Controller;

use App\Entity\GameRound;
use App\Entity\Player;
use App\Game\BlackJack;
use App\Repository\GameRoundRepository;
use App\Repository\PlayerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class ApiBlackJackController extends AbstractController
{
    #[Route('/proj/api/game/start', name: 'proj_api_game_start', methods: ['POST'])]
    public function start(
        Request $request,
        SessionInterface $session,
        PlayerRepository $playerRepo,
        EntityManagerInterface $em
    ): JsonResponse {
        $data = json_decode($request->getContent(), true) ?: [];
        $playerName = trim((string) ($data['playerName'] ?? $request->request->get('playerName', '')));
        $bet = (int) ($data['bet'] ?? $request->request->get('bet', 10));
        $hands = (int) ($data['hands'] ?? $request->request->get('hands', 1));

        if ($playerName === '') {
            return $this->json(['error' => 'playerName is required'], 400);
        }

        // Find or create player
        $player = $playerRepo->findByName($playerName);
        if (!$player) {
            $player = new Player();
            $player->setName($playerName);
            $em->persist($player);
            $em->flush();
        }

        $balance = $player->getBalance();
        $hands = max(1, min(3, $hands));
        $bet = max(1, $bet);

        if ($bet * $hands > $balance) {
            return $this->json(['error' => 'Insufficient balance', 'balance' => $balance], 400);
        }

        $game = new BlackJack($playerName, $balance);
        $game->deal($hands, $bet);
        $session->set('bj_api_game', $game);

        return $this->json($game->toArray());
    }

    #[Route('/proj/api/game/hit', name: 'proj_api_game_hit', methods: ['POST'])]
    public function hit(
        SessionInterface $session,
        PlayerRepository $playerRepo,
        EntityManagerInterface $em
    ): JsonResponse {
        $game = $session->get('bj_api_game');
        if (!$game instanceof BlackJack) {
            return $this->json(['error' => 'No active game. Start a game first.'], 400);
        }

        $game->hit();
        $session->set('bj_api_game', $game);

        if ($game->isGameOver()) {
            $this->saveApiResults($game, $playerRepo, $em);
        }

        return $this->json($game->toArray());
    }

    #[Route('/proj/api/game/stand', name: 'proj_api_game_stand', methods: ['POST'])]
    public function stand(
        SessionInterface $session,
        PlayerRepository $playerRepo,
        EntityManagerInterface $em
    ): JsonResponse {
        $game = $session->get('bj_api_game');
        if (!$game instanceof BlackJack) {
            return $this->json(['error' => 'No active game. Start a game first.'], 400);
        }

        $game->stand();
        $session->set('bj_api_game', $game);

        if ($game->isGameOver()) {
            $this->saveApiResults($game, $playerRepo, $em);
        }

        return $this->json($game->toArray());
    }

    #[Route('/proj/api/game/status', name: 'proj_api_game_status', methods: ['GET'])]
    public function status(SessionInterface $session): JsonResponse
    {
        $game = $session->get('bj_api_game');
        if (!$game instanceof BlackJack) {
            return $this->json(['error' => 'No active game.'], 400);
        }

        return $this->json($game->toArray());
    }

    #[Route('/proj/api/player/{name}/stats', name: 'proj_api_player_stats', methods: ['GET'])]
    public function playerStats(
        string $name,
        PlayerRepository $playerRepo,
        GameRoundRepository $roundRepo
    ): JsonResponse {
        $player = $playerRepo->findByName($name);
        if (!$player) {
            return $this->json(['error' => 'Player not found'], 404);
        }

        $rounds = $roundRepo->findBy(['player' => $player], ['createdAt' => 'DESC']);

        $stats = [
            'name' => $player->getName(),
            'balance' => $player->getBalance(),
            'totalRounds' => count($rounds),
            'wins' => 0,
            'losses' => 0,
            'pushes' => 0,
            'blackjacks' => 0,
        ];

        foreach ($rounds as $round) {
            $result = $round->getResult();
            if ($result === 'player_win' || $result === 'dealer_bust') {
                $stats['wins']++;
            } elseif ($result === 'blackjack') {
                $stats['blackjacks']++;
                $stats['wins']++;
            } elseif ($result === 'push') {
                $stats['pushes']++;
            } else {
                $stats['losses']++;
            }
        }

        $stats['recentRounds'] = array_map(function (GameRound $r) {
            return [
                'bet' => $r->getBet(),
                'result' => $r->getResult(),
                'playerScore' => $r->getPlayerScore(),
                'dealerScore' => $r->getDealerScore(),
                'createdAt' => $r->getCreatedAt()->format('Y-m-d H:i:s'),
            ];
        }, array_slice($rounds, 0, 10));

        return $this->json($stats);
    }

    private function saveApiResults(
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
