<?php

namespace App\Controller;

use App\Entity\Book;
use App\Repository\BookRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/books')]
class BookController extends AbstractController
{
    // Listar todos los libros con promedio de rating
    #[Route('', name: 'api_books_list', methods: ['GET'])]
    public function getBooks(BookRepository $bookRepository): JsonResponse
    {
        $booksWithRating = $bookRepository->findAllWithAverageRating();

        $result = [];
        foreach ($booksWithRating as $book) {
            $result[] = [
                'id' => $book['id'] ?? null,
                'title' => $book['title'],
                'author' => $book['author'],
                'published_year' => $book['published_year'],
                'average_rating' => $book['average_rating'] !== null ? (float) $book['average_rating'] : null,
                'reviews' => [] // siempre incluimos el array para consistencia
            ];
        }

        return $this->json($result);
    }

    // Obtener detalle de un libro (con reseñas incluidas)
    #[Route('/{id}', name: 'api_books_detail', methods: ['GET'])]
    public function getBook(Book $book): JsonResponse
    {
        $reviews = [];
        foreach ($book->getReviews() as $review) {
            $reviews[] = [
                'id' => $review->getId(),
                'rating' => $review->getRating(),
                'comment' => $review->getComment(),
                'created_at' => $review->getCreatedAt()->format('Y-m-d H:i:s')
            ];
        }

        return $this->json([
            'id' => $book->getId(),
            'title' => $book->getTitle(),
            'author' => $book->getAuthor(),
            'published_year' => $book->getPublishedYear(),
            'reviews' => $reviews
        ]);
    }

    // Crear un nuevo libro
    #[Route('', name: 'api_books_create', methods: ['POST'])]
    public function createBook(
        Request $request,
        BookRepository $bookRepository,
        ValidatorInterface $validator
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        if (!$data) {
            return $this->json(['error' => 'JSON inválido'], 400);
        }

        $book = new Book();
        $book->setTitle($data['title'] ?? '');
        $book->setAuthor($data['author'] ?? '');
        $book->setPublishedYear((int) ($data['published_year'] ?? 0));

        $errors = $validator->validate($book);
        if (count($errors) > 0) {
            $messages = [];
            foreach ($errors as $error) {
                $messages[$error->getPropertyPath()] = $error->getMessage();
            }
            return $this->json(['errors' => $messages], 400);
        }

        $bookRepository->save($book, true);

        return $this->json([
            'id' => $book->getId(),
            'title' => $book->getTitle(),
            'author' => $book->getAuthor(),
            'published_year' => $book->getPublishedYear(),
            'reviews' => [] // siempre incluimos el array
        ], 201);
    }

    // Actualizar un libro
    #[Route('/{id}', name: 'api_books_update', methods: ['PUT'])]
    public function updateBook(
        Request $request,
        Book $book,
        BookRepository $bookRepository,
        ValidatorInterface $validator
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        if (!$data) {
            return $this->json(['error' => 'JSON inválido'], 400);
        }

        if (isset($data['title'])) $book->setTitle($data['title']);
        if (isset($data['author'])) $book->setAuthor($data['author']);
        if (isset($data['published_year'])) $book->setPublishedYear((int) $data['published_year']);

        $errors = $validator->validate($book);
        if (count($errors) > 0) {
            $messages = [];
            foreach ($errors as $error) {
                $messages[$error->getPropertyPath()] = $error->getMessage();
            }
            return $this->json(['errors' => $messages], 400);
        }

        $bookRepository->save($book, true);

        $reviews = [];
        foreach ($book->getReviews() as $review) {
            $reviews[] = [
                'id' => $review->getId(),
                'rating' => $review->getRating(),
                'comment' => $review->getComment(),
                'created_at' => $review->getCreatedAt()->format('Y-m-d H:i:s')
            ];
        }

        return $this->json([
            'id' => $book->getId(),
            'title' => $book->getTitle(),
            'author' => $book->getAuthor(),
            'published_year' => $book->getPublishedYear(),
            'reviews' => $reviews
        ]);
    }

    // Eliminar un libro
    #[Route('/{id}', name: 'api_books_delete', methods: ['DELETE'])]
    public function deleteBook(Book $book, BookRepository $bookRepository): JsonResponse
    {
        $bookRepository->remove($book, true);
        return $this->json(null, 204);
    }
}
