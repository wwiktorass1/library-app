<?php

namespace App\Controller\Api;

use App\Entity\Book;
use App\Repository\BookRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;

class BookApiController extends AbstractController
{
    #[Route('/api/books', name: 'api_books_list', methods: ['GET'])]
    #[OA\Get(
        summary: "Get all books",
        responses: [
            new OA\Response(
                response: 200,
                description: "Returns the list of books",
                content: new OA\JsonContent(
                    type: "array",
                    items: new OA\Items(ref: "#/components/schemas/Book")
                )
            )
        ]
    )]
    public function list(BookRepository $repository): JsonResponse
    {
        $books = $repository->findAll();

        return $this->json($books, 200, [], ['groups' => ['book:read']]);
    }
}
