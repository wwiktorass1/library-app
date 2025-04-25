<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use App\Entity\Book;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase; 
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\AbstractBrowser;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

abstract class WebTestCaseBase extends WebTestCase
{
    protected ?EntityManagerInterface $em = null;
    protected ?UserPasswordHasherInterface $passwordHasher = null;
    protected ?AbstractBrowser $client = null;

    protected function setUp(): void
    {
        $this->client = static::createClient(); 
    
        $this->em = static::getContainer()->get(EntityManagerInterface::class);
        $this->passwordHasher = static::getContainer()->get(UserPasswordHasherInterface::class);
    }

    private function createUser(string $email = null): User
    {
        $user = new User();
        $user->setEmail($email ?? 'testuser_' . uniqid() . '@example.com');
        $user->setPassword(
            $this->passwordHasher->hashPassword($user, 'password')
        );
        $user->setRoles(['ROLE_USER']);

        $this->em->persist($user);
        $this->em->flush();

        return $user;
    }

    protected function createBook(string $title): Book
    {
        $book = new Book();
        $book->setTitle($title);
        $book->setAuthor('Author');
        $book->setIsbn('9783161484100');
        $book->setPublicationDate(new \DateTimeImmutable('2023-01-01'));
        $book->setGenre('Genre');
        $book->setCopies(5);

        $this->em->persist($book);
        $this->em->flush();

        return $book;
    }

    protected function tearDown(): void
    {
        if ($this->em) {
            $this->em->createQuery('DELETE FROM App\Entity\Book')->execute();
            $this->em->createQuery('DELETE FROM App\Entity\User')->execute();
            $this->em->close();
        }

        parent::tearDown();
    }

    protected function loginUser(): void
    {
        $user = $this->createUser();
        $this->client->loginUser($user);
    }
}
