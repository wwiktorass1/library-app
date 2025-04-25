<?php

namespace App\Tests\Functional;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Doctrine\ORM\EntityManagerInterface;

class BookEdgeCaseValidationTest extends WebTestCase
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
        $user->setEmail('edgecase@example.com');
        $user->setPassword('$2y$13$mhBY6T9lfXSevU3yevtkzuptPaKdSQKmUdKMtcIn80vfiJCIYwJ9i');
        $user->setRoles(['ROLE_USER']);
        $this->em->persist($user);
        $this->em->flush();

        return $user;
    }

    public function testNegativeCopies(): void
    {
        $user = $this->createTestUser();
        $this->client->loginUser($user);

        $crawler = $this->client->request('GET', '/book/new');
        $form = $crawler->selectButton('Save')->form([
            'book[title]' => 'Invalid Copies',
            'book[author]' => 'Author',
            'book[isbn]' => '9783161484100',
            'book[publicationDate]' => '2024-01-01',
            'book[genre]' => 'Drama',
            'book[copies]' => -3,
        ]);
        $this->client->submit($form);

        $this->assertSelectorTextContains('.copies-error', 'Copies must be zero or a positive number.');
    }

    public function testEmptyAuthor(): void
    {
        $user = $this->createTestUser();
        $this->client->loginUser($user);

        $crawler = $this->client->request('GET', '/book/new');
        $form = $crawler->selectButton('Save')->form([
            'book[title]' => 'Missing Author',
            'book[author]' => '',
            'book[isbn]' => '9783161484100',
            'book[publicationDate]' => '2024-01-01',
            'book[genre]' => 'Drama',
            'book[copies]' => 1,
        ]);

        $this->client->submit($form);

        $this->assertSelectorExists('.form-error-message');
        $this->assertSelectorTextContains('.author-error', 'This value should not be blank');
    }
}
