<?php

namespace App\Tests\Functional;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Doctrine\ORM\EntityManagerInterface;

class BookInvalidDataTest extends WebTestCase
{
    protected KernelBrowser $client;
    protected EntityManagerInterface $em;

    protected function setUp(): void
    {
        self::ensureKernelShutdown(); 
        $this->client = static::createClient();
        $this->em = $this->client->getContainer()->get('doctrine')->getManager(); 

        $connection = $this->em->getConnection();
        $platform = $connection->getDatabasePlatform();

        $connection->executeStatement('SET FOREIGN_KEY_CHECKS = 0');
        $connection->executeStatement($platform->getTruncateTableSQL('book', true));
        $connection->executeStatement($platform->getTruncateTableSQL('user', true));
        $connection->executeStatement('SET FOREIGN_KEY_CHECKS = 1');
    }

    private function ensureTestUserExists(): User
    {
        $user = new User();
        $user->setEmail('invalid_test@example.com');
        $user->setPassword('$2y$13$mhBY6T9lfXSevU3yevtkzuptPaKdSQKmUdKMtcIn80vfiJCIYwJ9i'); // test1234
        $user->setRoles(['ROLE_USER']);

        $this->em->persist($user);
        $this->em->flush();

        return $user;
    }

    public function testSubmitInvalidIsbn(): void
    {
        $user = $this->ensureTestUserExists();
        $this->client->loginUser($user);
    
        $crawler = $this->client->request('GET', '/book/new');
        $form = $crawler->selectButton('Save')->form([
            'book[title]' => 'Edge Case Book',
            'book[author]' => 'John Smith',
            'book[isbn]' => 'INVALID-ISBN-123',
            'book[publicationDate]' => '2024-01-01',
            'book[genre]' => 'Drama',
            'book[copies]' => 2,
        ]);
    
        $this->client->submit($form);
        file_put_contents('/tmp/html.html', $this->client->getResponse()->getContent());
    
        $this->assertStringContainsString('Please enter a valid ISBN-13.', $this->client->getResponse()->getContent());
    }
    
}
