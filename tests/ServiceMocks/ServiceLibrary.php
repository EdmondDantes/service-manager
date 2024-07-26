<?php
declare(strict_types=1);

namespace IfCastle\ServiceManager\ServiceMocks;

use IfCastle\ServiceManager\ServiceMethod;

class ServiceLibrary
{
    private array $books = [
        [
            'title' => 'The Book',
            'author' => 'John Doe'
        ],
        [
            'title' => 'The Other Book',
            'author' => 'Jane Doe'
        ]
    ];
    
    #[ServiceMethod]
    public function findBookByAuthor(string $authorName): array
    {
        return array_filter($this->books, fn($book) => $book['author'] === $authorName);
    }
    
    #[ServiceMethod]
    public function addBook(array $book): void
    {
        // Add a book to the library
        $this->books[] = $book;
    }
    
    #[ServiceMethod]
    public function getBooks(): array
    {
        return $this->books;
    }
    
    #[ServiceMethod]
    public function removeBook(string $title): void
    {
        // Remove a book from the library
        $this->books = array_filter($this->books, fn($book) => $book['title'] !== $title);
    }
}