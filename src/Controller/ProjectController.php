<?php

namespace App\Controller;

use App\Repository\GameRoundRepository;
use App\Repository\PlayerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProjectController extends AbstractController
{
    #[Route('/proj', name: 'proj_index')]
    public function index(): Response
    {
        return $this->render('proj/index.html.twig');
    }

    #[Route('/proj/about', name: 'proj_about')]
    public function about(): Response
    {
        return $this->render('proj/about.html.twig');
    }

    #[Route('/proj/about/database', name: 'proj_about_database')]
    public function aboutDatabase(): Response
    {
        return $this->render('proj/about_database.html.twig');
    }

    #[Route('/proj/api', name: 'proj_api')]
    public function api(): Response
    {
        return $this->render('proj/api.html.twig');
    }

    #[Route('/proj/stats', name: 'proj_stats')]
    public function stats(
        PlayerRepository $playerRepo,
        GameRoundRepository $roundRepo
    ): Response {
        $players = $playerRepo->findAll();
        $stats = [];

        foreach ($players as $player) {
            $rounds = $roundRepo->findBy(['player' => $player], ['createdAt' => 'DESC']);
            $wins = 0;
            $losses = 0;
            $pushes = 0;
            $blackjacks = 0;

            foreach ($rounds as $round) {
                $result = $round->getResult();
                if ($result === 'player_win' || $result === 'dealer_bust') {
                    $wins++;
                } elseif ($result === 'blackjack') {
                    $blackjacks++;
                    $wins++;
                } elseif ($result === 'push') {
                    $pushes++;
                } else {
                    $losses++;
                }
            }

            $total = count($rounds);
            $stats[] = [
                'name' => $player->getName(),
                'balance' => $player->getBalance(),
                'totalRounds' => $total,
                'wins' => $wins,
                'losses' => $losses,
                'pushes' => $pushes,
                'blackjacks' => $blackjacks,
                'winRate' => $total > 0 ? round(($wins / $total) * 100, 1) : 0,
                'recentRounds' => array_slice($rounds, 0, 10),
            ];
        }

        return $this->render('proj/stats.html.twig', [
            'stats' => $stats,
        ]);
    }
}
