<?php

namespace App\Tests\Functional;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Doctrine\ORM\EntityManagerInterface;

class BookAjaxValidationTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $em;

    protected function setUp(): void
    {
        self::ensureKernelShutdown();
        $this->client = static::createClient();
        $this->em = static::getContainer()->get('doctrine')->getManager();

        $connection = $this->em->getConnection();
        $platform = $connection->getDatabasePlatform();

        $connection->executeStatement('SET FOREIGN_KEY_CHECKS = 0');
        $connection->executeStatement($platform->getTruncateTableSQL('book', true));
        $connection->executeStatement($platform->getTruncateTableSQL('user', true));
        $connection->executeStatement('SET FOREIGN_KEY_CHECKS = 1');
    }

    public function testAjaxFormValidationErrors(): void
    {
        $user = new User();
        $user->setEmail('ajax_test@example.com');
        $user->setPassword('$2y$13$mhBY6T9lfXSevU3yevtkzuptPaKdSQKmUdKMtcIn80vfiJCIYwJ9i'); // "test1234"
        $user->setRoles(['ROLE_USER']);
        $this->em->persist($user);
        $this->em->flush();

        $this->client->loginUser($user);

        // Imituojam AJAX užklausą
        $this->client->xmlHttpRequest('POST', '/book/new', [
            'book' => [
                'title' => '',
                'author' => '',
                'isbn' => 'INVALID',
                'publicationDate' => '2099-01-01',
                'genre' => 'A',
                'copies' => -5,
            ]
        ]);

        $response = $this->client->getResponse();
        $this->assertSame(400, $response->getStatusCode());

        $data = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('errors', $data);

        $errors = $data['errors'];

        $this->assertArrayHasKey('title', $errors);
        $this->assertEquals('This value should not be blank.', $errors['title'][0]);

        $this->assertArrayHasKey('author', $errors);
        $this->assertEquals('This value should not be blank.', $errors['author'][0]);

        $this->assertArrayHasKey('isbn', $errors);
        $this->assertEquals('Please enter a valid ISBN-13.', $errors['isbn'][0]);

        $this->assertArrayHasKey('publicationDate', $errors);
        $this->assertEquals('Publication date cannot be in the future.', $errors['publicationDate'][0]);

        $this->assertArrayHasKey('genre', $errors);
        $this->assertEquals('This value is too short', $errors['genre'][0]);

        $this->assertArrayHasKey('copies', $errors);
        $this->assertEquals('Copies must be zero or a positive number.', $errors['copies'][0]);
    }
}
