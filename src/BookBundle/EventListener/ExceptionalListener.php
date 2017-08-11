<?php
namespace BookBundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class ExceptionalListener
{
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();
        $message = sprintf(
            'Invalid API Request: %s',
            $exception->getMessage()
            );

        // Customize your response object to display the exception details
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent(json_encode(['status' => 'error', 'message' => $message]));

        // HttpExceptionInterface is a special type of exception that
        // holds status code and header details
        if ($exception instanceof HttpExceptionInterface) {
            $response->setStatusCode($exception->getStatusCode());
            $response->headers->replace($exception->getHeaders());
        } else {
            $response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        // Send the modified response object to the event
        $event->setResponse($response);
    }
}