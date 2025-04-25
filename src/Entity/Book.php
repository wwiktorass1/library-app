<?php

namespace App\Entity;

use App\Repository\BookRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use OpenApi\Attributes as OA;

#[ORM\Entity(repositoryClass: BookRepository::class)]
#[OA\Schema(
    title: "Book",
    description: "A book entity representing a single library book",
    required: ["title", "author", "isbn", "publicationDate", "genre", "copies"]
)]
class Book
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[OA\Property(description: "The unique identifier of the book")]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 2, max: 255)]
    #[OA\Property(example: "The Great Gatsby")]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 2, max: 255)]
    #[OA\Property(example: "F. Scott Fitzgerald")]
    private ?string $author = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Isbn(type: "isbn13", message: "Please enter a valid ISBN-13.")]
    #[OA\Property(example: "9780306406157")]
    private ?string $isbn = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotNull]
    #[OA\Property(type: "string", format: "date", example: "2024-01-01")]
    #[Assert\LessThanOrEqual('today', message: 'Publication date cannot be in the future.')]
    private ?\DateTimeInterface $publicationDate = null;

    #[ORM\Column]
    #[Assert\NotNull]
    #[Assert\PositiveOrZero(message: "Copies must be zero or a positive number.")]
    #[OA\Property(example: 5)]
    private ?int $copies = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Genre should not be blank.")]
    #[Assert\Length(
        min: 2,
        max: 255,
        minMessage: "This value is too short",
        maxMessage: "This value is too long"
    )]
    #[OA\Property(example: "Fiction", description: "Genre of the book (min 2 characters)")]
    private ?string $genre = null;
    

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;
        return $this;
    }

    public function getAuthor(): ?string
    {
        return $this->author;
    }

    public function setAuthor(string $author): static
    {
        $this->author = $author;
        return $this;
    }

    public function getIsbn(): ?string
    {
        return $this->isbn;
    }

    public function setIsbn(string $isbn): static
    {
        $this->isbn = $isbn;
        return $this;
    }

    public function getPublicationDate(): ?\DateTimeInterface
    {
        return $this->publicationDate;
    }

    public function setPublicationDate(?\DateTimeInterface $publicationDate): static
    {
        $this->publicationDate = $publicationDate;
        return $this;
    }

    public function getGenre(): ?string
    {
        return $this->genre;
    }

    public function setGenre(string $genre): static
    {
        $this->genre = $genre;
        return $this;
    }

    public function getCopies(): ?int
    {
        return $this->copies;
    }

    public function setCopies(int $copies): static
    {
        $this->copies = $copies;
        return $this;
    }
}
