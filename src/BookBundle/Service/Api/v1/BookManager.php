<?php
/**
 * Created by PhpStorm.
 * User: Amr
 * Date: 8/9/2017
 * Time: 12:02 PM
 */

namespace BookBundle\Service\Api\v1;


use BookBundle\Entity\Book;
use Doctrine\ORM\EntityManager;
use PHPUnit\Framework\Exception;

class BookManager
{
    protected $em;
    protected $repo;
    protected $class;

    public function __construct(EntityManager $em, $class)
    {
        $this->em = $em;
        $this->class = $class;
        $this->repo = $em->getRepository($class);
    }

    public function createBook(Book $book, array $authors)
    {
        foreach($authors as $author){
            $author->addBook($book);
            $book->addAuthors($author);
        }

        try {
            $this->em->persist($book);
            $this->em->flush();
        } catch (\Exception $e) {
            throw new Exception($e->getMessage());
        }

        return $book;
    }
}