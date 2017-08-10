<?php
/**
 * Author Entity
 */
namespace BookBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="author")
 * @ORM\Entity(repositoryClass="BookBundle\Repository\AuthorRepository")
 */
class Author
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank(message="Name field can not be empty")
     */
    private $name;

    /**
     * @ORM\ManyToMany(targetEntity="Book", mappedBy="authors")
     */
    private $books;

    public function __construct()
    {
        $this->books = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getBooks()
    {
        return $this->books;
    }

    /**
     * @param mixed $books
     */
    public function setBooks($books)
    {
        $this->books = $books;
    }

    public function addBook(Book $book)
    {
        if($this->books->contains($book)){
            return;
        }

        $this->books->add($book);
        $book->addAuthors($this);
    }

    public function removeBook(Book $book)
    {
        if(!$this->books->contains($book)){
            return;
        }

        $this->books->removeElement($book);
        $book->removeAuthors($this);
    }

}