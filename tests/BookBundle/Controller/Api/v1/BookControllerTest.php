<?php

namespace Tests\BookBundle\Controller\Api\v1;

use BookBundle\Entity\Author;
use BookBundle\Entity\Book;
use GuzzleHttp\Client;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class BookControllerTest extends KernelTestCase
{

    private $em;
    private $client;
    private $author;
    private $book;

    protected function setUp()
    {
        self::bootKernel();
        $this->em = static::$kernel->getContainer()
                ->get('doctrine')
                ->getManager();

        $this->client = new Client([
            'base_uri' => 'http://localhost',
            'defaults' => [
                'exceptions' => false
            ]
        ]);

        $this->createAuthor();
        $this->createBook();
    }

    private function createAuthor()
    {
        $author = new Author();
        $author->setName("Test Author");
        $this->em->persist($author);
        $this->em->flush();
        $this->author = $author;
    }

    private function createBook()
    {
        $book = new Book();
        $book->setTitle('Title');
        $book->setIsbn('1234');
        $book->setDescription('Description');
        $book->setAuthors([$this->author]);
        $this->em->persist($book);
        $this->em->flush();
        $this->book = $book;
    }

    /**
     * Test Create Author
     */
    public function testCreateAuthor()
    {
        $this->assertTrue($this->author instanceof Author);
    }

    /**
     * Test Create Book
     */
    public function testPost()
    {
        $data = array(
            'title' => 'Book Title',
            'isbn'  => '12345',
            'description' => 'Book Description',
            'authors'   => $this->author->getId()
        );

        $response = $this->client->post('/api/v1/admin/book',[
            'json' => $data,
            'auth' => ['admin', 'admin']
        ]);

        $this->assertEquals(201, $response->getStatusCode());
        $data = json_decode($response->getBody(true), true);
        $this->assertArrayHasKey('book', $data);
        return $data['book'];
    }

    /**
     * Test update book
     */
    public function testPut()
    {
        $book = $this->em->getRepository(Book::class)->find($this->book->getId());
        $this->assertTrue($book instanceof Book);

        $authors = [];
        foreach($book->getAuthors() as $a){
            $authors[] = $a->getId();
        };

        /**
         * Testing Data
         */
        $data = array(
            'title' => 'Book Title 2',
            'isbn'  => '1234567',
            'description' => 'Book Description',
            'authors' => $authors
        );

        $response = $this->client->put('/api/v1/admin/book/'. $book->getId(),[
            'json' => $data,
            'auth' => ['admin', 'admin']
        ]);

        $this->assertEquals(201, $response->getStatusCode());
        $data = json_decode($response->getBody(true), true);
        $this->assertArrayHasKey('book', $data);
    }


    /**
     * Test Get Books
     */
    public function testGet()
    {
        $response = $this->client->get('/api/v1/book');
        $this->assertEquals(200, $response->getStatusCode());
        $data = json_decode($response->getBody(true), true);
        $this->assertArrayHasKey('books', $data);
    }

    /**
     * Test Get books by author Id
     */
    public function testGetBooksByAuthor()
    {
        $response = $this->client->get('/api/v1/book/author/' .  $this->author->getId());
        $this->assertEquals(200, $response->getStatusCode());
        $data = json_decode($response->getBody(true), true);
        $this->assertArrayHasKey('books', $data);
    }

    /**
     * @depends testPost
     * @param number $bookId
     */
    public function testDelete()
    {
        $response = $this->client->delete('/api/v1/admin/book/'. $this->book->getId(), ['auth' => ['admin', 'admin']]);
        $this->assertEquals(204, $response->getStatusCode());
    }
}