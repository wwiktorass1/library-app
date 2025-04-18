<?php

namespace App\Tests\Form;

use App\Entity\Book;
use App\Form\BookType;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;

class BookTypeTest extends TypeTestCase
{
    protected function getExtensions(): array
    {
        $validator = Validation::createValidatorBuilder()
            ->enableAttributeMapping() 
            ->getValidator();

        return [
            new ValidatorExtension($validator),
        ];
    }

    public function testSubmitEmptyData(): void
    {
        $formData = [
            'title' => '',
            'author' => '',
            'isbn' => '',
            'publicationDate' => '',
            'genre' => '',
            'copies' => ''
        ];

        $form = $this->factory->create(BookType::class);
        $form->submit($formData);

        $this->assertFalse($form->isValid(), 'Form should not be valid when all fields are empty');
        $this->assertGreaterThanOrEqual(1, $form->getErrors(true)->count(), 'There should be validation errors');
    }

    public function testInvalidIsbn(): void
    {
        $formData = [
            'title' => 'Book',
            'author' => 'Author',
            'isbn' => 'not-a-valid-isbn',
            'publicationDate' => '2023-01-01',
            'genre' => 'Fiction',
            'copies' => 2
        ];

        $form = $this->factory->create(BookType::class);
        $form->submit($formData);

        $this->assertFalse($form->isValid(), 'Form should not be valid with invalid ISBN');
        $this->assertNotEmpty($form->get('isbn')->getErrors(), 'ISBN field should have validation errors');
    }

    public function testSubmitValidData(): void
    {
        $formData = [
            'title' => 'Valid Title',
            'author' => 'Valid Author',
            'isbn' => '9783161484100',
            'publicationDate' => '2022-12-01',
            'genre' => 'Non-Fiction',
            'copies' => 3
        ];

        $model = new Book();
        $form = $this->factory->create(BookType::class, $model);
        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
        $this->assertTrue($form->isValid());

        $expected = new Book();
        $expected->setTitle('Valid Title');
        $expected->setAuthor('Valid Author');
        $expected->setIsbn('9783161484100');
        $expected->setPublicationDate(new \DateTime('2022-12-01'));
        $expected->setGenre('Non-Fiction');
        $expected->setCopies(3);

        $this->assertEquals($expected, $model);
    }
}
