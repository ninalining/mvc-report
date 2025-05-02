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
        $sessionData = $session->all(); // 获取所有会话数据
        dump($session->all()); // 调试会话数据

        return $this->render('session/index.html.twig', [
            'sessionData' => $sessionData, // 传递到模板
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
