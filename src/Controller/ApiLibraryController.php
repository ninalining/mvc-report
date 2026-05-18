<?php

namespace App\Controller;

use App\Repository\BookRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class ApiLibraryController extends AbstractController
{
    #[Route('/api/library/books', name: 'api_library_books', methods: ['GET'])]
    public function getAllBooks(BookRepository $bookRepo): JsonResponse
    {
        $books = $bookRepo->findAll();
        $data = array_map(fn ($book) => [
            'id' => $book->getId(),
            'title' => $book->getTitle(),
            'isbn' => $book->getIsbn(),
            'author' => $book->getAuthor(),
            'image' => $book->getImage(),
        ], $books);

        return $this->json($data);
    }

    #[Route('/api/library/book/{isbn}', name: 'api_library_book_by_isbn', methods: ['GET'])]
    public function getBookByIsbn(string $isbn, BookRepository $bookRepo): JsonResponse
    {
        $book = $bookRepo->findByIsbn($isbn);

        if (!$book) {
            return $this->json(['error' => 'Book not found'], 404);
        }

        return $this->json([
            'id' => $book->getId(),
            'title' => $book->getTitle(),
            'isbn' => $book->getIsbn(),
            'author' => $book->getAuthor(),
            'image' => $book->getImage(),
        ]);
    }
}
