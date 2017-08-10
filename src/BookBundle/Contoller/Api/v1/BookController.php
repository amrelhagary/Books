<?php
namespace BookBundle\Contoller\Api\v1;

use BookBundle\Entity\Author;
use BookBundle\Entity\Book;
use BookBundle\Form\BookType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use BookBundle\Util\Helper;

class BookController extends Controller
{

    /**
     * Get A List of all books Action
     * @return JsonResponse
     */
    public function listAction()
    {
        $books = $this->getDoctrine()->getRepository(Book::class)->findBooks();
        return new JsonResponse(['books' => $books]);
    }

    public function getBooksByAuthorAction($authorId)
    {
        $books = $this->getDoctrine()->getRepository(Book::class)->findBooksByAuthorId($authorId);
        return new JsonResponse(['books' => $books]);
    }

    /**
     * Create book Action
     * @param Request $request
     * @return JsonResponse
     */
    public function AddAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $body = $request->getContent();
        $data = json_decode($body, true);

        if(!$data){
            return new JsonResponse("Invalid Json data format", 500);
        }

        $book = new Book();
        $form = $this->createForm(BookType::class, $book);
        $form->submit($data);

        $authors = $em->getRepository(Author::class)->findBy(['id' => $data['authors']]);


        if(!$authors){
            return new JsonResponse("Authors not found", 404);
        }


        $validator = $this->get('validator');
        $errors = $validator->validate($book);

        if(count($errors) > 0){
            return new JsonResponse(["status" => "error", "message" => Helper::formatErrorMessage($errors)], 500);
        }else{

            $bookManager = $this->get('book_manager.api.v1');

            try {
                $bookManager->createBook($book, $authors);
            } catch (\Exception $e) {
                return new JsonResponse(["status" => "error", "message" => $e->getMessage()], 500);
            }

            return new JsonResponse(["status" => "ok", "book" => $book->getId()], 201);
        }
    }

    /**
     * Update book Action
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function editAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $body = $request->getContent();
        $data = json_decode($body, true);
        $id = intval($id);
        $book = $em->getRepository(Book::class)->findOneBy(['id' => $id]);

        if(!$book){
            return new JsonResponse("Book Not found", 500);
        }


        if(isset($data['authors']) && $data['authors'] !== null){
            $authors = $em->getRepository(Author::class)->findBy(['id' => $data['authors']]);

            if(!$authors){
                return new JsonResponse("Authors not found", 404);
            }

        }else{
            $authors = $book->getAuthors();
        }

        $form = $this->createForm(BookType::class, $book);
        $form->submit($data);

        $validator = $this->get('validator');
        $errors = $validator->validate($book);

        if(count($errors) > 0){
            return new JsonResponse(["status" => "error", "message" => Helper::formatErrorMessage($errors)], 500);
        }else{

            $bookManager = $this->get('book_manager.api.v1');

            try {
                $bookManager->createBook($book, $authors);
            } catch (\Exception $e) {
                return new JsonResponse(["status" => "error", "message" => $e->getMessage()], 500);
            }

            return new JsonResponse(["status" => "ok", "book" => $book->getId()], 201);
        }
    }


    /**
     * Delete Book Action
     * @param $id
     * @return JsonResponse
     */
    public function deleteAction($id)
    {
        $id = intval($id);
        $em = $this->getDoctrine()->getManager();
        $book = $em->getRepository(Book::class)->findOneBy(['id' => $id]);

        if(!$book){
            return new JsonResponse("Book Not found", 404);
        }

        try{
            $em->remove($book);
            $em->flush();
        }catch (\Exception $e){
            return new JsonResponse(["status"=> "error", "message" => $e->getMessage()], 500);
        }

        return new JsonResponse("resources deleted successfully", 204);

    }
}