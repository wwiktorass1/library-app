<?php

namespace App\Tests\Functional;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Doctrine\ORM\EntityManagerInterface;

class BookAdditionalEdgeCasesTest extends WebTestCase
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

    private function createTestUser(): User
    {
        $user = new User();
        $user->setEmail('edgecase2@example.com');
        $user->setPassword('$2y$13$mhBY6T9lfXSevU3yevtkzuptPaKdSQKmUdKMtcIn80vfiJCIYwJ9i');
        $user->setRoles(['ROLE_USER']);

        $this->em->persist($user);
        $this->em->flush();

        return $user;
    }

    public function testFuturePublicationDate(): void
    {
        $user = $this->createTestUser();
        $this->client->loginUser($user);

        $crawler = $this->client->request('GET', '/book/new');
        $form = $crawler->selectButton('Save')->form([
            'book[title]' => 'Future Book',
            'book[author]' => 'Author',
            'book[isbn]' => '9783161484100',
            'book[publicationDate]' => '2099-01-01',
            'book[genre]' => 'SciFi',
            'book[copies]' => 2,
        ]);
        $this->client->submit($form);

        $this->assertSelectorTextContains('.publicationDate-error', 'Publication date cannot be in the future.');
    }

    public function testEmptyTitle(): void
    {
        $user = $this->createTestUser();
        $this->client->loginUser($user);

        $crawler = $this->client->request('GET', '/book/new');
        $form = $crawler->selectButton('Save')->form([
            'book[title]' => '',
            'book[author]' => 'Author',
            'book[isbn]' => '9783161484100',
            'book[publicationDate]' => '2024-01-01',
            'book[genre]' => 'Horror',
            'book[copies]' => 3,
        ]);
        $this->client->submit($form);

        $this->assertSelectorTextContains('.title-error', 'This value should not be blank');
    }

    public function testTooShortGenre(): void
    {
        $user = $this->createTestUser();
        $this->client->loginUser($user);

        $crawler = $this->client->request('GET', '/book/new');
        $form = $crawler->selectButton('Save')->form([
            'book[title]' => 'Some Title',
            'book[author]' => 'Author',
            'book[isbn]' => '9783161484100',
            'book[publicationDate]' => '2024-01-01',
            'book[genre]' => 'A',
            'book[copies]' => 1,
        ]);
        $this->client->submit($form);

        $this->assertSelectorTextContains('.genre-error', 'This value is too short');
    }
}
