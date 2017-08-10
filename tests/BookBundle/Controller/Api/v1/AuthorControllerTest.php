<?php

namespace Tests\BookBundle\Controller\Api\v1;

use BookBundle\Entity\Author;
use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class AuthorControllerTest extends KernelTestCase
{
    private $em;
    private $client;
    private $author;

    public function setUp()
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
    }

    private function createAuthor()
    {
        $author = new Author();
        $author->setName("Test Author");
        $this->em->persist($author);
        $this->em->flush();
        $this->author = $author;
    }

    /**
     * Test Create Author
     * @return Author
     */
    public function testPost()
    {
        $data = array(
            'name' => 'Author Name'
        );

        $response = $this->client->post('/api/v1/admin/author', [
            'json' => $data,
            'auth' => ['admin', 'admin']
        ]);
        $this->assertEquals(201, $response->getStatusCode());
        $data = json_decode($response->getBody(true), true);
        $this->assertArrayHasKey('author', $data);
        return $data['author'];
    }

    /**
     * Test Get Author
     */
    public function testGet()
    {
        $response = $this->client->get('/api/v1/author');
        $this->assertEquals(200, $response->getStatusCode());
        $data = json_decode($response->getBody(true), true);
        $this->assertArrayHasKey('authors', $data);
    }

    /**
     * Test Update Author
     */
    public function testPut()
    {
        $data = array(
            'name' => 'Author name 2'
        );

        $response = $this->client->put('/api/v1/admin/author/'. $this->author->getId(),[
            'json' => $data,
            'auth' => ['admin', 'admin']
        ]);
        $this->assertEquals(201, $response->getStatusCode());
        $data = json_decode($response->getBody(true), true);
        $this->assertArrayHasKey('author', $data);
    }


    /**
     * Test Delete Author
     */
    public function testDelete()
    {
        $response = $this->client->delete('/api/v1/admin/author/'. $this->author->getId(), ['auth' => ['admin', 'admin']]);
        $this->assertEquals(204, $response->getStatusCode());
    }
}