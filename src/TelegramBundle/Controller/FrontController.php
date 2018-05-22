<?php

namespace TelegramBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Session\Session;
use Telegram\Bot\Api;
use Telegram\Bot;
use Symfony\Component\HttpFoundation\Response;
use TelegramBundle\Entity\Flux;
use TelegramBundle\Entity\Message;
use TelegramBundle\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use TelegramBundle\Helper\FileHelper;
use Vimeo\Vimeo;
use Njasm\Soundcloud\SoundcloudFacade;
//use SoundCloud\;



class FrontController extends Controller
{
    const TOKEN = '231759836:AAGZYBI18PVrv2GwqSrgM_fn1NWtdc7OUzY';
    private $telegram;

    /**
     * @Route("/231759836:AAGZYBI18PVrv2GwqSrgM_fn1NWtdc7OUzY/hook")
     */
    public function indexAction()
    {
        $this->telegram = new Api(self::TOKEN);
         $logger = $this->get('logger');

        $logger->error(' -------------------------------------');
        $updates = $this->telegram->getWebhookUpdates();
        $update =  json_decode(json_encode($updates), FALSE);
        $logger->error(' -------------------------------------');


        $this->log($updates);

        $msg = json_encode($updates, JSON_PRETTY_PRINT);
        if(strlen($msg) < 4095) {
            $response = $this->telegram->sendMessage([
                'chat_id' => '180160712',
                'text' => $msg
            ]);
        }
        $this->execAction($update);

        return new Response('{}', Response::HTTP_OK);
    }


    /**
     * @Route("/test/audio")
     */
    public function testAudioAction(){
        $clientID = "0cf1bf09a1ed18fdf6e1c69040845384";
        $clientSecret = "2fe2036feb2e69c0cee327134730250b";
        $username = "user-251925016";
        $password = "wooc120916";

        $facade = new SoundcloudFacade($clientID, $clientSecret);
        $facade->userCredentials($username, $password); // on success, access_token is set by default for next requests.

        $em = $this->getDoctrine()->getManager();
        $audios = $em->getRepository("TelegramBundle:Message")->findBy(array( "type" => "voice"  ),array('tid' => 'DESC'));
        foreach ($audios as $audio){
            if($audio->getText() == null) {
                $trackPath = 'files/voices/' . $audio->getTid() . '.oga';
                $name = $audio->getUser()->getFname() . " " . $audio->getUser()->getLname();
                $date = $audio->getDate();
                $flux = $audio->getFlux()->getName();
                $title = $flux . " : " . $name . ", " . $date->format("D j M y");

                $filename = 'files/pic/' . $audio->getUser()->getTid() . '_2.jpg';

                if (!file_exists($filename)) {
                    $filename = 'files/pic/' . $audio->getUser()->getTid() . '_1.jpg';
                    if (!file_exists($filename)) {
                        $filename = 'files/pic/' . $audio->getUser()->getTid() . '_0.jpg';
                        if (!file_exists($filename)) {
                            $filename = "src/burb_black.png";
                        }
                    }
                }

                $handle = fopen($filename, "r");
                $contents = fread($handle, filesize($filename));

                $params = array('title' => $title, 'artwork_data' => $contents);

                //$response = $facade->upload($trackPath, $filename, $params);
                //$id = $response->bodyObject()->id;
                //$audio->setText($id);
                //$em->persist($audio);
                echo $title . "</br></br></br>";

                fclose($handle);
            }
        }
        //$em->flush();
        return new Response('Tqt', Response::HTTP_OK);
    }

    /**
     * @Route("/test/video")
     */
    public function testVideoAction(){
        $lib = new \Vimeo\Vimeo("a749d7196f116306b1cccf40ac54654f55e99fa1", "OlAZIzrefOoxtE5iCjZb6mWkugRFS9NUrEoP0LkC5iqPDlEVt6CmRpvcJskxL4f9bWJ4YO335rRp4sBTdNoPLxQTr7V10WktK/YS6/yB0q4XmyQlFVHmiVNm1jPGG/SZ");
        $lib->setToken("03c135134d9056ef853f336b50447e7a");

        $em = $this->getDoctrine()->getManager();
        $videos = $em->getRepository("TelegramBundle:Message")->findBy(array( "type" => "video", "text" => NULL ),array('tid' => 'DESC'),10,0);
        foreach ($videos as $video){
            $response =  $lib->upload('files/videos/'. $video->getTid() .'.mp4', false);
            $response = "video/".explode("/", $response)[2];

            $video->setText($response);
            $em->persist($video);
            $em->flush();
            var_dump($response);
        }
         $em->flush();
        return new Response("Tqt", Response::HTTP_OK);
    }


    /**
     * @Route("/temp/hook")
     */
    public function indexTempAction()
    {
        $this->telegram = new Api(self::TOKEN);

        $updates2 = '{
    "update_id": 106204312,
    "message": {
        "message_id": 70900,
        "from": {
            "id": 291846170,
            "first_name": "Cristiana",
            "last_name": "Scoppa"
        },
        "chat": {
            "id": -242381742,
            "title": "A-ghost City",
            "type": "group",
            "all_members_are_administrators": false
        },
        "date": 1502849768,
        "voice": {
            "duration": 21,
            "mime_type": "audio\/mpeg",
            "file_id": "AwADBAADfgEAAtZLoVBTgOPWT6qX1QI",
            "file_size": 170517
        }
    }
}';

        $updates  = '{
    "update_id": 106204975,
    "message": {
        "message_id": 80791,
        "from": {
            "id": 180160712,
            "is_bot": false,
            "first_name": "Vincent",
            "last_name": "Giardina",
            "username": "giardii",
            "language_code": "fr-FR"
        },
        "chat": {
            "id": 180160712,
            "first_name": "Vincent",
            "last_name": "Giardina",
            "username": "giardii",
            "type": "private"
        },
        "date": 1503687241,
        "text": "tt"
    }
}';
        $updates = json_decode($updates);

        $this->execAction($updates);

        $this->log($updates);
        //return $this->render('TelegramBundle:Flux:about.html.twig');
        return new Response('{}', Response::HTTP_OK);
    }

    /**
     * @Route("/temp/updates")
     */
    public function indexUpdateAction()
    {
        $file = file_get_contents("updates/b22.json");
        $obj = json_decode($file);
        foreach($obj->result as $upd){
            if($upd->message->chat->id == "-143388438"){
                $this->execAction($upd);
                dump($upd);
            }
        }
        return new Response('{}', Response::HTTP_OK);
    }

    // This update DB from Telegram-cli
    // https://github.com/vysheng/tg
    // https://github.com/tvdstaaij/telegram-history-dump
    /**
     * @Route("/temp/jsonupdate")
     */
    public function updateFromJson(){
        $jsonFileUrl = "updates/telegram-cli/F_BURB_PEPE1.json";
        $json = file_get_contents($jsonFileUrl);
        $obj = json_decode($json);
        foreach($obj as $upd){
            $this->execAction($upd);
        }
        return new Response('{}', Response::HTTP_OK);
    }

    private function execAction($update){

        if(isset($update->message)){

            $fluxed = $this->isFluxed($update->message->chat->id);
            $m = new Message($update,$fluxed);

            $em = $this->getDoctrine()->getManager();


            if($m->isPersistedType()){
                $flux = $em->getRepository("TelegramBundle:Flux")->findOneBy(array("tid" => $update->message->chat->id));
                $m->setFlux($flux);

                $u = $em->getRepository("TelegramBundle:User")->findOneByTid($update->message->from->id);
                if(!isset($u) && isset($update->message->from->id)){
                    $u = $this->newUser($update->message->from,$m->getType());
                }
                $m->setUser($u);

                $meta = null;
                if($m->getType() == "voice"){
                    $meta = $this->prepareMetaSound($m);
                }
                $file = new FileHelper($m->getType(),$update,$meta);
                $url = $file->persistFile();
                //$url = $this->persistFile($m->getType(),$update);
                if(isset($url['url'])) {
                    $m->setUrl($url['url']);
                }
                if(isset($url['meta']['vimeoId'])){
                    $m->setText($url['meta']['vimeoId']);
                }
                if(isset($url['meta']['scId'])){
                    $m->setText($url['meta']['scId']);
                }

                if($m->getType() == "location"){
                    $loc = $this->getLocality($m->getLat(),$m->getLon());
                    $m->setLocality($loc);
                }

                $issetMessage = $em->getRepository("TelegramBundle:Message")->findOneBy(array('tid'=>$m->getTid() ));

                if($issetMessage == null){
                    $em->persist($m);
                }
                if($u->getTid() != false){
                    $em->persist($u);
                }
            }
            if($m->getType() == "new_group"){
                $flux = new Flux($update);
                $issetFlux = $em->getRepository("TelegramBundle:Flux")->findOneBy(array('tid'=>$flux->getTid()));
                if($issetFlux == null){
                    $this->sayWelcome($flux->getTid());
                    $em->persist($flux);
                }
            }
            if($m->getType() == "new_participant" || $m->getType() == "new_admin"){
                $user = $this->newUser($update->message->new_chat_participant,$m->getType());
                $issetUser = $em->getRepository("TelegramBundle:User")->findOneBy(array('tid'=>$user->getTid()));
                if($user->getTid() != false && $issetUser == null){
                    $em->persist($user);
                }
            }
            if($m->getType() == "updateFlux"){
                $this->updateFlux($update->message);
            }
            if($m->getFlux() !== null){
                if($m->getFlux()->getTid() == "-228335226"){
                  $ch = curl_init();
                  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                  curl_setopt($ch, CURLOPT_URL,
                      'http://biennalurb.fwd.wf/?postid='.$m->getTid()
                  );
                  $msg = curl_exec($ch);
                }
            }
            $em->flush();
        }
    }

    private function newUser($data,$type){
        $user = new User($data);
        if($type == "new_admin"){
            $user->setAdmin(true);
        }
        $user->setPicUrl($this->persistUserPic($user->getTid()));
        return $user;
    }

    private function persistUserPic($userId){
        $numberOfPic = null;

            if(isset($this->getFromCommand("getUserProfilePhotos?user_id=".$userId)["result"]["photos"][0])){
        $photos = $this->getFromCommand("getUserProfilePhotos?user_id=".$userId)["result"]["photos"][0];
        foreach ($photos as $key=>$photo){
            $url = $this->getFileUrl($photo["file_id"]);
            $ext = $this->getExtension($url);
            $this->downloadFile($url,"files/pic/".$userId."_".$key.".".$ext,false);
        }
        $numberOfPic = count($photos) - 1;}
        return (string) $numberOfPic;
    }
    private function getFromCommand($command){
        $url = "https://api.telegram.org/bot".self::TOKEN."/".$command;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        curl_close($ch);
        $object = json_decode($output, true);

        return $object;
    }

    private function getFileUrl($fileId){
        $object = $this->getFromCommand("getFile?file_id=".$fileId);
        $url = "https://api.telegram.org/file/bot".self::TOKEN."/".$object["result"]["file_path"];
        return $url;
    }

    private function downloadFile($url,$name,$temp){
        file_put_contents($name, fopen($url, 'r'));
    }

    private function log($data){
        $logger = $this->get('logger');
        $logger->info(' -------------------------------------');
        $logger->info(" +++ ".json_encode($data)." +++ ");
        $logger->info(' -------------------------------------');
    }
    private function getExtension($url){
        $path_parts = pathinfo($url);
        if(isset($path_parts['extension'])){
        return $path_parts['extension'];}
           else{ return null;}
    }
    private function isFluxed($chatId)
    {
        $em = $this->getDoctrine()->getManager();
        $flux = $em->getRepository("TelegramBundle:Flux")->findOneBy(array("tid" => $chatId));
        if($flux == null){
            return false;
        }else{
            return true;
        }
    }

    private function updateFlux($message){
        $functions = array("description","partners","name","subname","color");
        $function = Message::getHashtag($message->text);
        $admin = User::isAdmin($message->from->id);
        if(in_array($function,$functions) && $admin ){
            $em = $this->getDoctrine()->getEntityManager();
            $flux = $em->getRepository("TelegramBundle:Flux")->findOneByTid($message->chat->id);

            if($function == "description"){
                $flux->setDescription($this->removeHashtags($message->text));
            }
            if($function == "partners"){
                $flux->setPartners($this->removeHashtags($message->text));
            }
            if($function == "name"){
                $flux->setName($this->removeHashtags($message->text));
            }
            if($function == "subname"){
                $flux->setSubname($this->removeHashtags($message->text));
            }
            if($function == "color"){
                $flux->setColor("#" . $this->removeHashtags($message->text));
            }
            if($function == "startdate"){
                $dateString = $this->removeHashtags($message->text);
                $date = \DateTime::createFromFormat(" d.m.Y",$dateString);
                $flux->setStartDate($date);
            }
            if($function == "locality"){
                $flux->setLocation($this->removeHashtags($message->text));
            }
            $em->persist($flux);
            $em->flush();
            $this->sayOk($message->chat->id);
        }
    }
    private function removeHashtags($string){
        return preg_replace('/#\S+ */', '', $string);
    }
    private function sayWelcome($tid){
        $msg = "#info\nWelcome on your new flux !! I'm proud to help you on your project\nThank you for adding me to this flux 😎 \n\nBefore add anybody to this flux, first set up this flux. Set the name, description, partners, locality, start date (e: #startdate 01.08.2017),  subname(optional), and of course the color (hexa format, without the #).\nFor example, send me:\n\n#description This is my new description\n\nDone. I wish you'll enjoy my compagny and don't forget, for any problem, contact my best friend Vincent (@giardii)\n✈️✈️✈️";
        $this->telegram->sendMessage([
            'chat_id' => $tid,
            'text' => $msg
        ]);
    }
    private function sayOk($tid){
        $msg = "#info \nDone 👌 ";
        $this->telegram->sendMessage([
            'chat_id' => $tid,
            'text' => $msg
        ]);
    }

    private function getLocality($lat, $lon){
        $url = "https://maps.googleapis.com/maps/api/geocode/json?latlng=" . $lat. "," . $lon;
        $json = file_get_contents($url);
        $resp = json_decode($json);

        if($resp->status == "OK" ) {
            $city = $resp->results[0]->address_components[3]->long_name;
            $counrty = $resp->results[0]->address_components[6]->long_name;
            $loc = $city . ", " . $counrty;
        }else{
            $loc = " ";
        }
        return $loc;
    }

    private function prepareMetaSound($audio){
        $name = $audio->getUser()->getFname()." ".$audio->getUser()->getLname() ;
        $date = $audio->getDate();
        $flux = $audio->getFlux()->getName();
        $title = $flux ." : ". $name .", ".$date->format("D j M y");

        $filename = 'files/pic/'.$audio->getUser()->getTid().'_2.jpg';

        if(!file_exists($filename)) {
            $filename = 'files/pic/'.$audio->getUser()->getTid().'_1.jpg';
            if(!file_exists($filename)){
                $filename = 'files/pic/'.$audio->getUser()->getTid().'_0.jpg';
                if(!file_exists($filename)){
                    $filename = "src/burb_black.png";
                }
            }
        }
        return array("title" => $title, "filename" => $filename);
    }
}
