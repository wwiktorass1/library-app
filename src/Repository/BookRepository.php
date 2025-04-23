<?php

namespace App\Repository;

use App\Entity\Book;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Book>
 */
class BookRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Book::class);
    }

}
public function searchByTitleOrAuthor(?string $query): array
{
    $query = trim((string) $query);

    // Jeigu paieška tuščia arba per trumpa – grąžinam tuščią masyvą
    if ($query === '' || strlen($query) < 2) {
        return [];
    }

    return $this->createQueryBuilder('b')
        ->where('b.title LIKE :q OR b.author LIKE :q')
        ->setParameter('q', '%' . $query . '%')
        ->getQuery()
        ->getResult();
}