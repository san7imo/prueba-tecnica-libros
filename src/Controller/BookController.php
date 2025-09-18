<?php

namespace App\Controller;

use App\Repository\BookRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api')]
class BookController extends AbstractController
{
    #[Route('/books', name: 'api_books', methods: ['GET'])]
    public function getBooks(BookRepository $bookRepository): JsonResponse
    {
        $booksWithRating = $bookRepository->findAllWithAverageRating();
        
        $result = [];
        foreach ($booksWithRating as $book) {
            $result[] = [
                'title' => $book['title'],
                'author' => $book['author'],
                'published_year' => $book['published_year'],
                'average_rating' => $book['average_rating'] ? (float) $book['average_rating'] : null
            ];
        }
        
        return $this->json($result);
    }
}