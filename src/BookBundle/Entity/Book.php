<?php
/**
 * Book Entity
 */
namespace BookBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="book")
* @ORM\Entity(repositoryClass="BookBundle\Repository\BookRepository")
 */
class Book
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     */
    private $title;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank()
     */
    private $isbn;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     */
    private $description;

    /**
     * @ORM\ManyToMany(targetEntity="Author", inversedBy="books")
     * @ORM\JoinTable(name="books_authors",
     *      joinColumns={@ORM\JoinColumn(name="book_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="author_id", referencedColumnName="id")}
     * )
     */
    private $authors;

    public function __construct()
    {
        $this->authors = new ArrayCollection();
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
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return mixed
     */
    public function getIsbn()
    {
        return $this->isbn;
    }

    /**
     * @param mixed $isbn
     */
    public function setIsbn($isbn)
    {
        $this->isbn = $isbn;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getAuthors()
    {
        return $this->authors;
    }

    /**
     * @param mixed $authors
     */
    public function setAuthors($authors)
    {
        $this->authors = $authors;
    }

    public function addAuthors(Author $author)
    {
        if($this->authors->contains($author)){
            return;
        }

        $this->authors->add($author);
        $author->addBook($this);
    }

    public function removeAuthors(Author $author)
    {
        if(!$this->authors->contains($author)){
            return;
        }

        $this->authors->removeElement($author);
        $author->removeBook($this);
    }

}