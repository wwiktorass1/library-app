<?php

namespace App\Tests\Functional;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Entity\Book;

class BookSearchTest extends WebTestCase
{
    private $client;
    private $container;

    protected function setUp(): void
    {
        self::ensureKernelShutdown();

        $this->client = static::createClient();
        $this->container = $this->client->getContainer();
        $em = $this->container->get('doctrine')->getManager();

        $connection = $em->getConnection();
        $platform = $connection->getDatabasePlatform();

        $connection->executeStatement('SET FOREIGN_KEY_CHECKS = 0');
        $connection->executeStatement($platform->getTruncateTableSQL('book', true));
        $connection->executeStatement($platform->getTruncateTableSQL('user', true));
        $connection->executeStatement('SET FOREIGN_KEY_CHECKS = 1');
    }

    private function ensureTestUserExists(): User
    {
        $em = $this->container->get('doctrine')->getManager();

        $user = $em->getRepository(User::class)
            ->findOneBy(['email' => 'naujokas@example.com']);

        if (!$user) {
            $user = new User();
            $user->setEmail('naujokas@example.com');
            $user->setPassword('$2y$13$mhBY6T9lfXSevU3yevtkzuptPaKdSQKmUdKMtcIn80vfiJCIYwJ9i'); // test1234
            $user->setRoles(['ROLE_USER']);

            $em->persist($user);
            $em->flush();
        }

        return $user;
    }
    public function testSearchReturnsResults(): void
    {
        $user = $this->ensureTestUserExists();
        $this->client->loginUser($user);
    
        $book = new Book();
        $book->setTitle('Test Book');
        $book->setAuthor('John Doe');
        $book->setIsbn('9780306406157');
        $book->setPublicationDate(new \DateTimeImmutable('2024-01-01'));
        $book->setGenre('Fiction');
        $book->setCopies(1);
    
        $em = $this->container->get('doctrine')->getManager();
        $em->persist($book);
        $em->flush();
    
        $this->client->request('GET', '/book/search?q=Test');
    
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('.book-item');
        $this->assertSelectorTextContains('.book-item', 'Test Book');
    }

    public function testSearchWithNoResults(): void
    {
        $user = $this->ensureTestUserExists();
        $this->client->loginUser($user);

        $crawler = $this->client->request('GET', '/book/search?q=nonexistenttitle');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('div', 'No books found.');
    }

    public function testCreateBookWithValidData(): void
    {
        $user = $this->ensureTestUserExists();
        $this->client->loginUser($user);

        $crawler = $this->client->request('GET', '/book/new');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('form');

        $form = $crawler->selectButton('Save')->form([
            'book[title]' => 'Test Book',
            'book[author]' => 'John Doe',
            'book[isbn]' => '9780306406157',
            'book[publicationDate]' => '2024-01-01',
            'book[genre]' => 'Fiction',
            'book[copies]' => 3,
        ]);

        $this->client->submit($form);
        $this->assertResponseRedirects('/book');
        $this->client->followRedirect();

        $this->assertSelectorTextContains('.book-item', 'Test Book');
        $this->assertSelectorTextContains('.book-item', 'John Doe');
    }
}
