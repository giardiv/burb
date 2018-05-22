<?php

namespace ApiCineBundle\Controller;

use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Telegram;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Telegram\Bot\Api;
use Telegram\Bot;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{

    public function indexAction()
    {
        return $this->render('ApiCineBundle:Default:index.html.twig');
    }
    /**
     * @Route("/hook")
     */
    public function hookAction()
    {
        $API_KEY = '231759836:AAGZYBI18PVrv2GwqSrgM_fn1NWtdc7OUzY';
        $BOT_NAME = 'BurbBot';
        
        $logger = $this->get('logger');
        $telegram = new Api($API_KEY);
        
        
        $logger->error(' -------------------------------------');
        $updates = $telegram->getWebhookUpdates();
        $logger->error(" +++ ".json_encode($updates)." +++ ");
        $logger->error(' -------------------------------------');
       
        $msg = json_encode($updates, JSON_PRETTY_PRINT);
 
        $response = $telegram->sendMessage([
            'chat_id' => '180160712', //$updates['message']['chat']['id'], 
            'text' => $msg
        ]);
        
        
        return new Response('{}', Response::HTTP_OK);
    }
}
