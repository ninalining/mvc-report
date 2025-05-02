<?php

// src/Controller/PageController.php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

class PageController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function home(): Response
    {
        return $this->render('home.html.twig');
    }

    #[Route('/about', name: 'about')]
    public function about(): Response
    {
        return $this->render('about.html.twig');
    }

    #[Route('/report', name: 'report')]
    public function report(): Response
    {
        return $this->render('report.html.twig');
    }

    #[Route('/lucky', name: 'lucky')]
    public function lucky(): Response
    {
        $number = random_int(1, 100);
        return $this->render('lucky.html.twig', [
            'number' => $number
        ]);
    }

    #[Route('/api', name: 'api_index')]
    public function apiIndex(): Response
    {
        return $this->render('api/index.html.twig');
    }

    #[Route('/api/quote', name: 'api_quote')]
    public function quote(): JsonResponse
    {
        $quotes = [
            'Do not go where the path may lead, go instead where there is no path and leave a trail.',
            'The only way to do great work is to love what you do.',
            'Life is what happens when you’re busy making other plans.'
        ];

        $random = $quotes[array_rand($quotes)];

        return (new JsonResponse(
            [
                'quote' => $random,
                'date' => date('Y-m-d'),
                'time' => date('H:i:s'),
            ]
        ))->setEncodingOptions(JSON_PRETTY_PRINT);
    }
}
