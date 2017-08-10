<?php
namespace BookBundle\Repository;

use BookBundle\Entity\Book;
use Doctrine\ORM\EntityRepository;

class BookRepository extends EntityRepository
{

    public function findBooks()
    {
        return $this->getEntityManager()
            ->createQuery('SELECT b.isbn, b.title, b.description FROM BookBundle:Book b ORDER BY b.id')
            ->getResult();
    }

    public function findBooksByAuthorId($authorId)
    {
        $query =  $this->getEntityManager()
            ->createQueryBuilder('e')
            ->select('b', 'a')
            ->from('BookBundle:Book', 'b')
            ->leftJoin('b.authors', 'a')
            ->where('a.id = :authorId')
            ->setParameter('authorId', $authorId)
            ->getQuery();

        $result = $query->getResult();
        $books = [];
        if($result){
            foreach($result as $book){
                $books[] = [
                    'isbn' => $book->getIsbn(),
                    'title' => $book->getTitle(),
                    'description' => $book->getDescription()
                ];
            }
        }

        return $books;
    }
}