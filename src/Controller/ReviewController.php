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

#[Route('/api')]
class ReviewController extends AbstractController
{
    #[Route('/reviews', name: 'api_reviews_create', methods: ['POST'])]
    public function createReview(
        Request $request,
        BookRepository $bookRepository,
        ReviewRepository $reviewRepository,
        ValidatorInterface $validator
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);
        
        if (!$data) {
            return $this->json(['error' => 'JSON inválido'], 400);
        }

        // Validar que book_id existe
        if (!isset($data['book_id'])) {
            return $this->json(['error' => 'book_id es requerido'], 400);
        }

        $book = $bookRepository->find($data['book_id']);
        if (!$book) {
            return $this->json(['error' => 'El libro especificado no existe'], 400);
        }

        // Crear la review
        $review = new Review();
        $review->setBook($book);
        
        if (isset($data['rating'])) {
            $review->setRating($data['rating']);
        }
        
        if (isset($data['comment'])) {
            $review->setComment($data['comment']);
        }

        // Validar la entidad
        $errors = $validator->validate($review);
        
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[$error->getPropertyPath()] = $error->getMessage();
            }
            
            return $this->json(['errors' => $errorMessages], 400);
        }

        // Guardar la review
        $reviewRepository->save($review, true);

        return $this->json([
            'id' => $review->getId(),
            'book_id' => $review->getBook()->getId(),
            'rating' => $review->getRating(),
            'comment' => $review->getComment(),
            'created_at' => $review->getCreatedAt()->format('Y-m-d H:i:s')
        ], 201);
    }
}