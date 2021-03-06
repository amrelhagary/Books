<?php
namespace BookBundle\Controller\Api\v1;

use BookBundle\Entity\Author as AuthorEntity;
use BookBundle\Entity\Author;
use BookBundle\Form\AuthorType;
use BookBundle\Util\Helper;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class AuthorController extends Controller
{
    public function listAction()
    {
        $authors = $this->getDoctrine()->getRepository(Author::class)->findAuthors();
        return new JsonResponse(["authors" => $authors]);
    }

    public function addAction(Request $request)
    {
        $logger = $this->get('logger');
        $em = $this->getDoctrine()->getManager();
        $body = $request->getContent();
        $data = json_decode($body, true);

        if(!$data){
            $logger->error('Invalid json data format');
            return new JsonResponse(['status' => 'error', 'message' => 'Invalid Json data format'], 500);
        }

        $author = new AuthorEntity();
        $form = $this->createForm(AuthorType::class, $author);
        $form->submit($data);

        $validator = $this->get('validator');
        $errors = $validator->validate($author);

        if(count($errors) > 0){
            $logger->error('Error validating author form: ' . Helper::formatErrorMessage($errors));
            return new JsonResponse(["status" => "error", "message" => Helper::formatErrorMessage($errors)], 500);
        }else{
            try{
                $em->persist($author);
                $em->flush();
            }catch (\Exception $e){
                $logger->error('Error saving data: ' . $e->getMessage());
                return new JsonResponse(["status" => "error", "message" => $e->getMessage()], 500);
            }
            return new JsonResponse(["status" => "ok", "author" => $author->getId()], 201);
        }
    }

    public function editAction(Request $request, $id)
    {
        $logger = $this->get('logger');
        $id = intval($id);
        $em = $this->getDoctrine()->getManager();
        $body = $request->getContent();
        $data = json_decode($body, true);

        if(!$data){
            $logger->error('Invalid json data format');
            return new JsonResponse("Invalid Json data format", 500);
        }

        $author = $em->getRepository(Author::class)->findOneBy(['id' => $id]);

        if(!$author){
            $logger->error('Author not found');
            return new JsonResponse("Author not found", 404);
        }

        $form = $this->createForm(AuthorType::class, $author);
        $form->submit($data);

        $validator = $this->get('validator');
        $errors = $validator->validate($author);

        if(count($errors) > 0){
            $logger->error('Error validating author form: ' . Helper::formatErrorMessage($errors));
            return new JsonResponse(["status" => "error", "message" => Helper::formatErrorMessage($errors)], 500);
        }else{
            try{
                $em->persist($author);
                $em->flush();
            }catch (\Exception $e){
                $logger->error('Error saving data: ' . $e->getMessage());
                return new JsonResponse(["status" => "error", "message" => $e->getMessage()], 500);
            }
            return new JsonResponse(["status" => "ok", "author" => $author->getId()], 201);
        }
    }

    public function deleteAction(Request $request, $id)
    {
        $logger = $this->get('logger');
        $id = intval($id);
        $em = $this->getDoctrine()->getManager();
        $author = $em->getRepository(Author::class)->findOneBy(['id' => $id]);

        if(!$author){
            $logger->error('Author not found');
            return new JsonResponse("Book Not found", 404);
        }

        try{
            $em->remove($author);
            $em->flush();
        }catch (\Exception $e){
            $logger->error('Error saving data: ' . $e->getMessage());
            return new JsonResponse(["status"=> "error", "message" => $e->getMessage()], 500);
        }

        return new JsonResponse(["status"=> "ok", "message" => "resources deleted successfully"], 204);
    }
}