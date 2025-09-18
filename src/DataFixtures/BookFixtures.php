<?php

namespace App\DataFixtures;

use App\Entity\Book;
use App\Entity\Review;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class BookFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Crear los 3 libros requeridos
        $books = [
            [
                'title' => 'El Arte de Programar',
                'author' => 'Donald Knuth',
                'published_year' => 1968
            ],
            [
                'title' => 'Clean Code',
                'author' => 'Robert C. Martin',
                'published_year' => 2008
            ],
            [
                'title' => 'Refactoring',
                'author' => 'Martin Fowler',
                'published_year' => 1999
            ]
        ];

        $bookEntities = [];
        
        foreach ($books as $bookData) {
            $book = new Book();
            $book->setTitle($bookData['title']);
            $book->setAuthor($bookData['author']);
            $book->setPublishedYear($bookData['published_year']);
            
            $manager->persist($book);
            $bookEntities[] = $book;
        }

        // Crear las reviews (6+ total, variedad de ratings)
        $reviews = [
            // Para "El Arte de Programar"
            [
                'book_index' => 0,
                'rating' => 5,
                'comment' => 'Un libro fundamental para cualquier programador. Muy completo.'
            ],
            [
                'book_index' => 0,
                'rating' => 4,
                'comment' => 'Excelente contenido pero puede ser denso para principiantes.'
            ],
            [
                'book_index' => 0,
                'rating' => 5,
                'comment' => 'La biblia de la programación. Imprescindible.'
            ],
            // Para "Clean Code"
            [
                'book_index' => 1,
                'rating' => 4,
                'comment' => 'Cambió mi forma de escribir código. Muy recomendado.'
            ],
            [
                'book_index' => 1,
                'rating' => 3,
                'comment' => 'Buenos principios, aunque algunos ejemplos son repetitivos.'
            ],
            // Para "Refactoring"
            [
                'book_index' => 2,
                'rating' => 5,
                'comment' => 'Técnicas esenciales para mejorar código existente.'
            ]
        ];

        foreach ($reviews as $reviewData) {
            $review = new Review();
            $review->setBook($bookEntities[$reviewData['book_index']]);
            $review->setRating($reviewData['rating']);
            $review->setComment($reviewData['comment']);
            
            $manager->persist($review);
        }

        $manager->flush();
    }
}