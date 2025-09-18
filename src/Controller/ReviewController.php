<?php

namespace App\Controller;

use App\Entity\Review;
use App\Repository\BookRepository;
use App\Repository\ReviewRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/reviews')]
class ReviewController extends AbstractController
{
    // Crear una nueva reseña
    #[Route('', name: 'api_reviews_create', methods: ['POST'])]
    public function create(
        Request $request,
        BookRepository $bookRepository,
        ReviewRepository $reviewRepository,
        ValidatorInterface $validator
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);
        if (!$data) {
            return $this->json(['error' => 'JSON inválido'], 400);
        }

        $book = $bookRepository->find($data['book_id'] ?? null);
        if (!$book) {
            return $this->json(['error' => 'El libro no existe'], 400);
        }

        $review = new Review();
        $review->setBook($book);
        $review->setRating((int) ($data['rating'] ?? 0));
        $review->setComment($data['comment'] ?? '');

        $errors = $validator->validate($review);
        if (count($errors) > 0) {
            $messages = [];
            foreach ($errors as $error) {
                $messages[$error->getPropertyPath()] = $error->getMessage();
            }
            return $this->json(['errors' => $messages], 400);
        }

        $reviewRepository->save($review, true);

        return $this->json([
            'id' => $review->getId(),
            'book_id' => $review->getBook()->getId(),
            'rating' => $review->getRating(),
            'comment' => $review->getComment(),
            'created_at' => $review->getCreatedAt()->format('Y-m-d H:i:s')
        ], 201);
    }

    // Obtener una reseña por ID
    #[Route('/{id}', name: 'api_reviews_detail', methods: ['GET'])]
    public function getOne(Review $review): JsonResponse
    {
        return $this->json([
            'id' => $review->getId(),
            'book_id' => $review->getBook()->getId(),
            'rating' => $review->getRating(),
            'comment' => $review->getComment(),
            'created_at' => $review->getCreatedAt()->format('Y-m-d H:i:s')
        ]);
    }

    // Actualizar reseña existente
    #[Route('/{id}', name: 'api_reviews_update', methods: ['PUT'])]
    public function update(
        Request $request,
        Review $review,
        ReviewRepository $reviewRepository,
        ValidatorInterface $validator
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);
        if (!$data) {
            return $this->json(['error' => 'JSON inválido'], 400);
        }

        if (isset($data['rating'])) $review->setRating((int) $data['rating']);
        if (isset($data['comment'])) $review->setComment($data['comment']);

        $errors = $validator->validate($review);
        if (count($errors) > 0) {
            $messages = [];
            foreach ($errors as $error) {
                $messages[$error->getPropertyPath()] = $error->getMessage();
            }
            return $this->json(['errors' => $messages], 400);
        }

        $reviewRepository->save($review, true);

        return $this->json([
            'id' => $review->getId(),
            'book_id' => $review->getBook()->getId(),
            'rating' => $review->getRating(),
            'comment' => $review->getComment(),
            'created_at' => $review->getCreatedAt()->format('Y-m-d H:i:s')
        ]);
    }

    // Eliminar reseña
    #[Route('/{id}', name: 'api_reviews_delete', methods: ['DELETE'])]
    public function delete(Review $review, ReviewRepository $reviewRepository): JsonResponse
    {
        $reviewRepository->remove($review, true);
        return $this->json(null, 204);
    }
}
