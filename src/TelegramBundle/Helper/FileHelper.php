<?php

namespace TelegramBundle\Helper;

use FFMpeg\FFMpeg;
use FFMpeg\Format\Video\Ogg;
use FFMpeg\Format\Video\WebM;
use TelegramBundle\Controller\FrontController;
use Vimeo\Vimeo;
use Njasm\Soundcloud\SoundcloudFacade;

class FileHelper
{
    public $id;
    private $type;
    private $update;


    function __construct($type,$update,$meta){
        $this->id = $update->update_id;
        $this->type = $type;
        $this->update = $update;
        $this->meta = $meta;
    }

    private function setId($id){
        $this->id = $id;
    }
    private function getId(){
        return $this->id;
    }

    public function persistFile(){
        $meta = array();
        $url = null;
        if($this->type == "photo"){
            foreach ($this->update->message->photo as $key=>$photo){

                if($this->update->message->chat->type == "manuallyUpdated"){
                    $url = "http://biennaleurbana.com/updates/telegram-cli/media/" . $photo->file_id;
                }else{
                    $url = $this->getFileUrl($photo->file_id);
                }
                $ext = $this->getExtension($url);
                $this->downloadFile($url,"files/photos/".$this->id."_".$key.".".$ext,false);
            }
            $numberOfPic = count($this->update->message->photo) - 1;
            return (string) $numberOfPic;
        }
        if($this->type == "video"){
            if($this->update->message->chat->type == "manuallyUpdated"){
              $url = "http://biennaleurbana.com/updates/telegram-cli/media/" . $this->update->message->video->file_id;
            }else{
              $url = $this->getFileUrl($this->update->message->video->file_id);
            }
            $ext = $this->getExtension($url);
            $this->downloadFile($url,"files/videos/".$this->id.".".$ext,true);
            $vimeoId = $this->uploadToVimeo($this->id);
            $meta['vimeoId'] = $vimeoId;
        }
        if($this->type == "voice"){
            if($this->update->message->chat->type == "manuallyUpdated"){
              $url = "http://biennaleurbana.com/updates/telegram-cli/media/" . $this->update->message->voice->file_id;
            }else{
              $url = $this->getFileUrl($this->update->message->voice->file_id);
            }
            $ext = $this->getExtension($url);
            $this->downloadFile($url,"files/voices/".$this->id.".".$ext,true);
            $scId = $this->uploadToSoundCloud($this->id);
            $meta['scId'] = $scId;
        }
        if($this->type == "document"){
            if($this->update->message->chat->type == "manuallyUpdated"){
              $url = "http://biennaleurbana.com/updates/telegram-cli/media/" . $this->update->message->document->file_id;
            }else{
              $url = $this->getFileUrl($this->update->message->document->file_id);
            }
            $ext = $this->getExtension($url);
            $this->downloadFile($url,"files/documents/".$this->id.".".$ext,true);
        }
        return array("url" => $url,"meta" => $meta);
    }

    private function getFileUrl($fileId){
        $object = $this->getFromCommand("getFile?file_id=".$fileId);
        $url = "https://api.telegram.org/file/bot".FrontController::TOKEN."/".$object["result"]["file_path"];
        return $url;
    }

    private function downloadFile($url,$name,$temp){
        file_put_contents($name, fopen($url, 'r'));
    }
    public static function getExtension($url){
        $path_parts = pathinfo($url);
        if(isset($path_parts['extension'])){
            return $path_parts['extension'];}
        else{ return null;}
    }

    private function getFromCommand($command){
        $url = "https://api.telegram.org/bot".FrontController::TOKEN."/".$command;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        curl_close($ch);
        $object = json_decode($output, true);

        return $object;
    }

    private function convertVideo(){
        $ffmpeg = FFMpeg::create();
        $video = $ffmpeg->open("files/videos/". $this->id .".mp4");
        $video->save(new WebM(),"files/videos/". $this->id .".webm");
        $video->save(new Ogg(),"files/videos/". $this->id .".ogg");
    }
    public static function convertVideoFromId($id){
        $ffmpeg = FFMpeg::create();
        $video = $ffmpeg->open("files/videos/". $id .".mp4");
        $video->save(new WebM(),"files/videos/". $id .".webm");
        $video->save(new Ogg(),"files/videos/". $id .".ogg");
    }
    private function converAudio(){
        $ffmpeg = FFMpeg::create();
        $audio = $ffmpeg->open("files/voices/". $this->id .".oga");
    }

    private function uploadToVimeo($id){
        $lib = new \Vimeo\Vimeo("a749d7196f116306b1cccf40ac54654f55e99fa1", "OlAZIzrefOoxtE5iCjZb6mWkugRFS9NUrEoP0LkC5iqPDlEVt6CmRpvcJskxL4f9bWJ4YO335rRp4sBTdNoPLxQTr7V10WktK/YS6/yB0q4XmyQlFVHmiVNm1jPGG/SZ");
        $lib->setToken("03c135134d9056ef853f336b50447e7a");

        $response =  $lib->upload('files/videos/'. $id .'.mp4', false);
        $response = "video/".explode("/", $response)[2];

        return $response;
    }
    public function uploadToSoundCloud($id){
        $clientID = "0cf1bf09a1ed18fdf6e1c69040845384";
        $clientSecret = "2fe2036feb2e69c0cee327134730250b";
        $username = "biennaleurbana";
        $password = "wooc120916";

        $facade = new SoundcloudFacade($clientID, $clientSecret);
        $facade->userCredentials($username, $password); // on success, access_token is set by default for next requests.


        $trackPath = 'files/voices/'. $id .'.oga';
        if(!file_exists($trackPath)){
            $trackPath = 'files/voices/'. $id .'.ogg';
        }


        $handle = fopen($this->meta['filename'], "r");
        $contents = fread($handle, filesize($this->meta['filename']));

        $params = array('title' => $this->meta['title'], 'artwork_data' => $contents);

        $response = $facade->upload($trackPath, $this->meta['filename'], $params);
        $id = $response->bodyObject()->id;

        fclose($handle);

        return $id;
    }

}
