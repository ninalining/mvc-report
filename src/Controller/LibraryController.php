<?php

namespace App\Controller;

use App\Entity\Book;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LibraryController extends AbstractController
{
    #[Route('/library', name: 'library_index')]
    public function index(): Response
    {
        return $this->render('library/index.html.twig');
    }

    #[Route('/library/books', name: 'library_books')]
    public function books(BookRepository $bookRepo): Response
    {
        $books = $bookRepo->findAll();
        return $this->render('library/books.html.twig', [
            'books' => $books,
        ]);
    }

    #[Route('/library/book/{id}', name: 'library_book_show')]
    public function show(Book $book): Response
    {
        return $this->render('library/book.html.twig', [
            'book' => $book,
        ]);
    }

    #[Route('/library/create', name: 'library_create', methods: ['GET'])]
    public function create(): Response
    {
        return $this->render('library/form.html.twig', [
            'book' => null,
            'action' => $this->generateUrl('library_create_post'),
        ]);
    }

    #[Route('/library/create', name: 'library_create_post', methods: ['POST'])]
    public function createPost(Request $request, EntityManagerInterface $em): Response
    {
        $book = new Book();
        $book->setTitle($request->request->get('title'));
        $book->setIsbn($request->request->get('isbn'));
        $book->setAuthor($request->request->get('author'));

        $this->handleImageUpload($request, $book);

        $em->persist($book);
        $em->flush();

        $this->addFlash('success', 'Boken har lagts till!');
        return $this->redirectToRoute('library_books');
    }

    #[Route('/library/edit/{id}', name: 'library_edit', methods: ['GET'])]
    public function edit(Book $book): Response
    {
        return $this->render('library/form.html.twig', [
            'book' => $book,
            'action' => $this->generateUrl('library_edit_post', ['id' => $book->getId()]),
        ]);
    }

    #[Route('/library/edit/{id}', name: 'library_edit_post', methods: ['POST'])]
    public function editPost(Book $book, Request $request, EntityManagerInterface $em): Response
    {
        $book->setTitle($request->request->get('title'));
        $book->setIsbn($request->request->get('isbn'));
        $book->setAuthor($request->request->get('author'));

        $this->handleImageUpload($request, $book);

        $em->flush();

        $this->addFlash('success', 'Boken har uppdaterats!');
        return $this->redirectToRoute('library_book_show', ['id' => $book->getId()]);
    }

    #[Route('/library/delete/{id}', name: 'library_delete', methods: ['POST'])]
    public function delete(Book $book, EntityManagerInterface $em): Response
    {
        $em->remove($book);
        $em->flush();

        $this->addFlash('success', 'Boken har raderats!');
        return $this->redirectToRoute('library_books');
    }

    #[Route('/library/reset', name: 'library_reset')]
    public function reset(EntityManagerInterface $em, BookRepository $bookRepo): Response
    {
        // Remove all books
        foreach ($bookRepo->findAll() as $book) {
            $em->remove($book);
        }
        $em->flush();

        // Add default books
        $defaults = [
            ['Katten Misse', '978-91-00-00001-1', 'Linnéa Leewong', 'cat.jpg'],
            ['Hunden Bella', '978-91-00-00002-8', 'Linnéa Leewong', 'dog.jpg'],
            ['Kaninen Snöboll', '978-91-00-00003-5', 'Linnéa Leewong', 'rabbit.jpg'],
        ];

        foreach ($defaults as [$title, $isbn, $author, $image]) {
            $book = new Book();
            $book->setTitle($title);
            $book->setIsbn($isbn);
            $book->setAuthor($author);
            $book->setImage($image);
            $em->persist($book);
        }
        $em->flush();

        $this->addFlash('success', 'Biblioteket har återställts!');
        return $this->redirectToRoute('library_books');
    }

    private function handleImageUpload(Request $request, Book $book): void
    {
        $file = $request->files->get('image');
        if ($file) {
            $filename = uniqid() . '.' . $file->guessExtension();
            $file->move($this->getParameter('kernel.project_dir') . '/public/img', $filename);
            $book->setImage($filename);
        }
    }
}
