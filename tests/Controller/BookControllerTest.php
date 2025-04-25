<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Entity\Book;
use App\Entity\User;
use App\Tests\Functional\WebTestCaseBase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class BookControllerTest extends WebTestCaseBase
{
    private array $booksToRemove = [];
    private array $usersToRemove = [];

    protected function tearDown(): void
    {
        $em = static::getContainer()->get('doctrine')->getManager();
        $bookRepo = $em->getRepository(Book::class);
        $userRepo = $em->getRepository(User::class);

        foreach ($this->booksToRemove as $bookId) {
            $book = $bookRepo->find($bookId);
            if ($book) {
                $em->remove($book);
            }
        }

        foreach ($this->usersToRemove as $userId) {
            $user = $userRepo->find($userId);
            if ($user) {
                $em->remove($user);
            }
        }
        $em->flush();
        parent::tearDown();
    }

    protected function createUser(string $email = 'testuser@example.com', string $password = 'password123'): User
    {
        $em = static::getContainer()->get('doctrine')->getManager();
        $hasher = static::getContainer()->get(UserPasswordHasherInterface::class);
        $user = new User();
        $user->setEmail($email);
        $user->setRoles(['ROLE_USER']);
        $user->setPassword($hasher->hashPassword($user, $password));
        $em->persist($user);
        $em->flush();
        $this->usersToRemove[] = $user->getId();
        return $user;
    }

    protected function createBook(string $title = 'Test Book'): Book
    {
        $em = static::getContainer()->get('doctrine')->getManager();
        $book = new Book();
        $book->setTitle($title);
        $book->setAuthor('Test Author');
        $book->setIsbn('1234567890');
        $book->setPublicationDate(new \DateTime('2023-01-01'));
        $book->setGenre('Test Genre');
        $book->setCopies(1);
        $em->persist($book);
        $em->flush();
        $this->booksToRemove[] = $book->getId();
        return $book;
    }

    public function testIndexRequiresLogin(): void
    {
        $this->client->request('GET', '/book');
        $this->assertResponseRedirects('/login', Response::HTTP_FOUND);
    }

    public function testIndexAsLoggedInUser(): void
    {
        $user = $this->createUser();
        $this->client->loginUser($user);

        $crawler = $this->client->request('GET', '/book');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Book Index');
    }

    public function testNew(): void
    {
        $this->loginUser();
        $this->client->loginUser($this->createUser());
        $crawler = $this->client->request('GET', '/book/new');
        $this->assertResponseIsSuccessful();

        $form = $crawler->filter('form#book-form')->form([
            'book[title]' => 'Test Title',
            'book[author]' => 'Test Author',
            'book[isbn]' => '9783161484100',
            'book[publicationDate]' => '2025-01-01',
            'book[genre]' => 'Test Genre',
            'book[copies]' => 5,
        ]);

        $this->client->submit($form);
        $this->assertResponseRedirects('/book');
        $this->client->followRedirect();
        $this->assertSelectorTextContains('.book-item', 'Test Title');
    }

    public function testShow(): void
    {
        $book = $this->createBook('Detail Book');
        $this->client->loginUser($this->createUser());
        $this->client->request('GET', '/book/' . $book->getId());
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Book');
    }

    public function testEdit(): void
    {
        $this->loginUser();
        $book = $this->createBook('Old Title');
        $this->client->loginUser($this->createUser());
        $crawler = $this->client->request('GET', '/book/' . $book->getId() . '/edit');
        $this->assertResponseIsSuccessful();

        $form = $crawler->selectButton('Update')->form([
            'book[title]' => 'Updated Title',
            'book[author]' => 'Updated Author',
            'book[isbn]' => '9783161484100',
            'book[publicationDate]' => '2024-01-01',
            'book[genre]' => 'Updated Genre',
            'book[copies]' => 10,
        ]);

        $this->client->submit($form);
        $this->assertResponseRedirects('/book');
    }

    public function testRemove(): void
    {
        $this->loginUser();
        $book = $this->createBook('Delete Me');
    
        $crawler = $this->client->request('GET', '/book');
    
        $form = $crawler->selectButton('Delete')->form();
    
        $this->client->submit($form);
    
        $this->assertResponseRedirects('/book');
    }

    public function testSearchReturnsResult(): void
    {
        $this->loginUser();
        $this->createBook('UniqueTitleOne');
        $this->client->request('GET', '/book?q=UniqueTitleOne');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('.book-item', 'UniqueTitleOne');
    }

    public function testSearchReturnsNoResults(): void
    {
        $this->loginUser();
        $this->createBook('Unrelated Book');
        $this->client->request('GET', '/book?q=NoMatchTerm');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('.book-list');
    }

    public function testEditInvalidData(): void
    {
        $this->loginUser();
        $book = $this->createBook('Original Title');

        $crawler = $this->client->request('GET', '/book/' . $book->getId() . '/edit');

        $form = $crawler->filter('form[name="book"]')->form([
            'book[title]' => '', 
            'book[author]' => 'Author',
            'book[isbn]' => '9783161484100',
            'book[publicationDate]' => '2023-01-01',
            'book[genre]' => 'Genre',
            'book[copies]' => 5,
        ]);

        $this->client->submit($form);

        $this->assertResponseStatusCodeSame(422); 
        $this->assertSelectorExists('#book_title + .invalid-feedback');
        $this->assertSelectorTextContains('#book_title + .invalid-feedback', 'This value should not be blank.');
    }


    public function testDeleteWithoutLogin(): void
    {
        $book = $this->createBook('Protected Book');

        $this->client->request('POST', '/book/' . $book->getId());

        $this->assertResponseRedirects('/login');
    }

}
