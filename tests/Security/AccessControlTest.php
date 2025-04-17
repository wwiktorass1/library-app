<?php

namespace App\Tests\Security;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AccessControlTest extends WebTestCase
{
    public function testIndexRequiresLogin(): void
    {
        $client = static::createClient();
        $client->request('GET', '/book');

        self::assertResponseRedirects('/login');
    }
}