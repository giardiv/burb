<?php

namespace TelegramBundle\EventListener;

use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class ExceptionListener
{
    /**
     * Holds Symfony2 router
     *
     *@var Router
     */
    protected $router;

    /**
     * @param Router
     */
    public function __construct(Router $router)
    {
        $this->router = $router;
    }


    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();

        if ($exception instanceof NotFoundHttpException) {

            /** Choose your router here
            $route = 'home';

            if ($route === $event->getRequest()->get('_route')) {
                return;
            }

            $url = $this->router->generate($route);
            $response = new RedirectResponse($url);
            $event->setResponse($response); */

        }
    }
}