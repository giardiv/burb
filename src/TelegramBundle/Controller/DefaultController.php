<?php

namespace TelegramBundle\Controller;

use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Telegram;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Longman\TelegramBot;
use Telegram\Bot;
use Telegram\Bot\Api;

class DefaultController extends Controller
{
    /**
     * @Route("/hook")
     */
    public function indexAction()
    {
        $logger = $this->get('logger');
        $logger->info('I just got the logger');
        $logger->error('An error occurred');
        return $this->render('TelegramBundle:Default:index.html.twig');
    }
    /**
     * @Route("/cook")
     */
    public function createWHAction(){
        $API_KEY = '231759836:AAGZYBI18PVrv2GwqSrgM_fn1NWtdc7OUzY';
        $BOT_NAME = 'BurbBot';
        $hook_url = 'https://biennaleurbana.com/apicine/hook';

        $telegram = new Api($API_KEY);
        //$response = $telegram->getMe();

        $response = $telegram->setWebhook([
            'url' => $hook_url,
            'certificate' => 'YOURPUBLIC.pem'
        ]);
        dump($response);
        //dump($response);

        return $this->render('TelegramBundle:Default:index.html.twig');
    }
}
