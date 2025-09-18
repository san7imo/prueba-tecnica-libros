<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\ReviewRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ReviewRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['review:read']],
    denormalizationContext: ['groups' => ['review:write']]
)]
class Review
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['review:read', 'book:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'reviews')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: 'El libro es requerido')]
    #[Groups(['review:read', 'review:write'])]
    private ?Book $book = null;

    #[ORM\Column]
    #[Assert\NotNull(message: 'El rating es requerido')]
    #[Assert\Range(
        min: 1,
        max: 5,
        notInRangeMessage: 'El rating debe estar entre {{ min }} y {{ max }}'
    )]
    #[Groups(['review:read', 'review:write', 'book:read'])]
    private ?int $rating = null;

    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank(message: 'El comentario no puede estar vacío')]
    #[Groups(['review:read', 'review:write', 'book:read'])]
    private ?string $comment = null;

    #[ORM\Column(type: 'datetime')]
    #[Groups(['review:read', 'book:read'])]
    private ?\DateTimeInterface $created_at = null;

    public function __construct()
    {
        $this->created_at = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBook(): ?Book
    {
        return $this->book;
    }

    public function setBook(?Book $book): static
    {
        $this->book = $book;

        return $this;
    }

    public function getRating(): ?int
    {
        return $this->rating;
    }

    public function setRating(int $rating): static
    {
        $this->rating = $rating;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(string $comment): static
    {
        $this->comment = $comment;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }
}
