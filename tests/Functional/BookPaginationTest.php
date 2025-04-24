<?php

namespace App\Tests\Functional;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BookPaginationTest extends WebTestCase
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
        $user = $em->getRepository(User::class)->findOneBy(['email' => 'naujokas@example.com']);

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

    public function testBookIndexPagination(): void
    {
        $user = $this->ensureTestUserExists();
        $this->client->loginUser($user);

        $crawler = $this->client->request('GET', '/book?page=1');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('.pagination');
        $this->assertGreaterThanOrEqual(1, $crawler->filter('tbody tr')->count());
        $this->assertLessThanOrEqual(10, $crawler->filter('tbody tr')->count());

        $crawler = $this->client->request('GET', '/book?page=2');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('.pagination');
        $this->assertGreaterThanOrEqual(0, $crawler->filter('tbody tr')->count());
    }
}
