<?php
namespace BookBundle\Repository;

use Doctrine\ORM\EntityRepository;

class AuthorRepository extends EntityRepository
{
    public function findAuthors()
    {
        return $this->getEntityManager()
            ->createQuery(
                'SELECT a.id, a.name FROM BookBundle:Author a ORDER BY a.id'
            )
            ->getResult();
    }
}