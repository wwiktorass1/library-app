<?php

namespace App\Tests\Controller;

use App\Entity\Book;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class BookControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $bookRepository;
    private string $path = '/book/';

    protected function setUp(): void
    {
        $this->client = static::createClient(); 
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->bookRepository = $this->manager->getRepository(Book::class);

        // Remove all books
        foreach ($this->bookRepository->findAll() as $object) {
            $this->manager->remove($object);
        }

        // Remove all users
        $this->manager->createQuery('DELETE FROM App\Entity\User')->execute();

        $this->manager->flush();
    }

    public function testIndexRequiresLogin(): void
    {
        $this->client->request('GET', '/book');

        self::assertResponseRedirects('/login');
    }

    public function testIndexAsLoggedInUser(): void
    {
        $user = new User();
        $user->setEmail('test_'.uniqid().'@example.com');
        $user->setPassword('$2y$13$mhBY6T9lfXSevU3yevtkzuptPaKdSQKmUdKMtcIn80vfiJCIYwJ9i'); // "test1234"
        $user->setRoles(['ROLE_USER']);

        $this->manager->persist($user);
        $this->manager->flush();

        $this->client->loginUser($user);
        $crawler = $this->client->request('GET', '/book');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Book index');
    }

    public function testNew(): void
    {
        $user = new User();
        $user->setEmail('new_'.uniqid().'@example.com');
        $user->setPassword('$2y$13$mhBY6T9lfXSevU3yevtkzuptPaKdSQKmUdKMtcIn80vfiJCIYwJ9i');
        $user->setRoles(['ROLE_USER']);
        $this->manager->persist($user);
        $this->manager->flush();
        $this->client->loginUser($user);

        $this->client->request('GET', sprintf('%snew', $this->path));
        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'book[title]' => 'Testing',
            'book[author]' => 'Author',
            'book[isbn]' => '9783161484100',
            'book[publicationDate]' => '2023-01-01',
            'book[genre]' => 'Test',
            'book[copies]' => 5,
        ]);

        self::assertResponseRedirects('/book');
        self::assertSame(1, $this->bookRepository->count([]));
    }

    public function testShow(): void
    {
        $user = new User();
        $user->setEmail('show_'.uniqid().'@example.com');
        $user->setPassword('$2y$13$mhBY6T9lfXSevU3yevtkzuptPaKdSQKmUdKMtcIn80vfiJCIYwJ9i');
        $user->setRoles(['ROLE_USER']);
        $this->manager->persist($user);
        $this->manager->flush();
        $this->client->loginUser($user);

        $book = new Book();
        $book->setTitle('My Title');
        $book->setAuthor('Author');
        $book->setIsbn('1234567890');
        $book->setPublicationDate(new \DateTime('2023-01-01'));
        $book->setGenre('Genre');
        $book->setCopies(3);

        $this->manager->persist($book);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $book->getId()));
        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Book');
    }

    public function testEdit(): void
    {
        $user = new User();
        $user->setEmail('edit_'.uniqid().'@example.com');
        $user->setPassword('$2y$13$mhBY6T9lfXSevU3yevtkzuptPaKdSQKmUdKMtcIn80vfiJCIYwJ9i');
        $user->setRoles(['ROLE_USER']);
        $this->manager->persist($user);
        $this->manager->flush();
        $this->client->loginUser($user);

        $book = new Book();
        $book->setTitle('Old Title');
        $book->setAuthor('Old Author');
        $book->setIsbn('111');
        $book->setPublicationDate(new \DateTime('2022-01-01'));
        $book->setGenre('Old');
        $book->setCopies(1);

        $this->manager->persist($book);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $book->getId()));
        $this->client->submitForm('Update', [
            'book[title]' => 'New Title',
            'book[author]' => 'New Author',
            'book[isbn]' => '9783161484100',
            'book[publicationDate]' => '2023-01-01',
            'book[genre]' => 'New',
            'book[copies]' => 10,
        ]);

        self::assertResponseRedirects('/book');
        $updatedBook = $this->bookRepository->find($book->getId());

        self::assertSame('New Title', $updatedBook->getTitle());
    }

    public function testRemove(): void
    {
        $user = new User();
        $user->setEmail('remove_'.uniqid().'@example.com');
        $user->setPassword('$2y$13$mhBY6T9lfXSevU3yevtkzuptPaKdSQKmUdKMtcIn80vfiJCIYwJ9i');
        $user->setRoles(['ROLE_USER']);
        $this->manager->persist($user);
        $this->manager->flush();
        $this->client->loginUser($user);

        $book = new Book();
        $book->setTitle('Title');
        $book->setAuthor('Author');
        $book->setIsbn('333');
        $book->setPublicationDate(new \DateTime('2023-01-01'));
        $book->setGenre('Genre');
        $book->setCopies(2);

        $this->manager->persist($book);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $book->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/book');
        self::assertSame(0, $this->bookRepository->count([]));
    }
}
