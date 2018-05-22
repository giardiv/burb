<?php

namespace TelegramBundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use TelegramBundle\Helper\FileHelper;

class TerminateListener
{
    public function onKernelTerminate($event)
    {
        $request = $event->getRequest();
        if ($request->attributes->get('_route') == 'home') {
            //FileHelper::convertVideoFromId("300137925");
        }
    }
}