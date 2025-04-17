<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BookAccessTest extends WebTestCase
{
    public function testRedirectToLoginIfNotAuthenticated(): void
    {
        $client = static::createClient();
        $client->request('GET', '/book');

        $this->assertResponseRedirects('/login');
    }
}

