<?php

namespace App\Controller;

use App\Entity\Book;
use App\Form\BookType;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Knp\Component\Pager\PaginatorInterface;
use OpenApi\Attributes as OA;
use Nelmio\ApiDocBundle\Attribute\Model;

#[OA\Tag(name: 'Books')]
final class BookController extends AbstractController
{
    #[Route('/book', name: 'app_book_index', methods: ['GET'])]
    #[OA\Get(
        summary: 'Get paginated list of books',
        parameters: [
            new OA\Parameter(name: 'page', in: 'query', schema: new OA\Schema(type: 'integer'), required: false, description: 'Page number')
        ],
        responses: [
            new OA\Response(response: 200, description: 'Returns list of books')
        ]
    )]
    public function index(Request $request, BookRepository $bookRepository, PaginatorInterface $paginator): Response
    {
        $queryBuilder = $bookRepository->createQueryBuilder('b');

        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('book/index.html.twig', [
            'books' => $pagination,
        ]);
    }

    #[Route('/book/new', name: 'app_book_new', methods: ['GET', 'POST'])]
    #[OA\Post(
        summary: 'Create a new book (supports AJAX)',
        description: 'This endpoint handles book creation via HTML form or JavaScript (AJAX) submission. If submitted via AJAX, it returns 204 No Content on success and 400 on validation error.',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: new Model(type: Book::class))
        ),
        responses: [
            new OA\Response(response: 201, description: 'Book created successfully'),
            new OA\Response(response: 204, description: 'Book created successfully via AJAX (no content)'),
            new OA\Response(response: 400, description: 'Validation error (e.g., when submitting via AJAX)')
        ]
    )]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $book = new Book();
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($book);
            $entityManager->flush();

            if ($request->isXmlHttpRequest()) {
                return new Response(null, 201); 
            }

            return $this->redirectToRoute('app_book_index');
        }

        if ($request->isXmlHttpRequest()) {
            return $this->render('book/_form.html.twig', [
                'form' => $form,
            ], new Response('', 400));
        }

        return $this->render('book/new.html.twig', [
            'book' => $book,
            'form' => $form,
        ]);
    }

    #[Route('/book/search', name: 'app_book_search', methods: ['GET'])]
    #[OA\Get(
        summary: 'Search books by title or author',
        parameters: [
            new OA\Parameter(name: 'q', in: 'query', required: true, schema: new OA\Schema(type: 'string'), description: 'Search query')
        ],
        responses: [
            new OA\Response(response: 200, description: 'Search results')
        ]
    )]
    public function search(Request $request, BookRepository $bookRepository): Response
    {
        $query = $request->query->get('q', '');
        $books = $bookRepository->searchByTitleOrAuthor($query);

        return $this->render('book/_list.html.twig', [
            'books' => $books,
        ]);
    }

    #[Route('/book/{id}', name: 'app_book_show', methods: ['GET'])]
    #[OA\Get(
        summary: 'Get a single book by ID',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Book found'),
            new OA\Response(response: 404, description: 'Book not found')
        ]
    )]
    public function show(?Book $book): Response
    {
        if (!$book) {
            throw $this->createNotFoundException('Book not found');
        }

        return $this->render('book/show.html.twig', [
            'book' => $book,
        ]);
    }

    #[Route('/book/{id}/edit', name: 'app_book_edit', methods: ['GET', 'POST'])]
    #[OA\Put(
        summary: 'Edit an existing book',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: new Model(type: Book::class))
        ),
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Book updated'),
            new OA\Response(response: 400, description: 'Invalid input')
        ]
    )]
    public function edit(Request $request, Book $book, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_book_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('book/edit.html.twig', [
            'book' => $book,
            'form' => $form,
        ]);
    }

    #[Route('/book/{id}', name: 'app_book_delete', methods: ['POST'])]
    #[OA\Delete(
        summary: 'Delete a book',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 204, description: 'Book deleted'),
            new OA\Response(response: 403, description: 'Invalid CSRF token')
        ]
    )]
    public function delete(Request $request, Book $book, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $book->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($book);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_book_index', [], Response::HTTP_SEE_OTHER);
    }
}
