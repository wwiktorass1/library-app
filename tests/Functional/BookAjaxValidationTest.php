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

        $crawler = $this->client->request('GET', '/book/new');
        $form = $crawler->selectButton('Save')->form([
            'book[title]' => '',
            'book[author]' => '',
            'book[isbn]' => 'INVALID',
            'book[publicationDate]' => '2099-01-01',
            'book[genre]' => 'A',
            'book[copies]' => -5,
        ]);

        $this->client->xmlHttpRequest('POST', '/book/new', $form->getPhpValues());

        $response = $this->client->getResponse();
        $this->assertSame(400, $response->getStatusCode());

        $this->assertStringContainsString('title-error', $response->getContent());
        $this->assertStringContainsString('author-error', $response->getContent());
        $this->assertStringContainsString('isbn-error', $response->getContent());
        $this->assertStringContainsString('publicationDate-error', $response->getContent());
        $this->assertStringContainsString('genre-error', $response->getContent());
        $this->assertStringContainsString('copies-error', $response->getContent());
    }
}
