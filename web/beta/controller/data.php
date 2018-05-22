<?php

//namespace TestBundle\Controller;

//use Symfony\Bundle\FrameworkBundle\Controller\Controller;
//use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
//require_once 'whatsprot.class.php';


class Data
{
    private $colorMin = 0;
    private $colorMax = 250;
    private $user = [];
    
    
    
    public function indexAction()
    {
        $r = [];
        $ts = 1458280800;
        //$ts = 1458220344;
        $msg['result'] = $this->getFromFile();
        foreach($msg['result'] as $m){
            if(in_array($m['message']['chat']['id'],$this->getGroups()) && 
               $m['message']['date'] > $ts &&
               $m['message']['date'] < 1458645017 &&
               !$this->isInfo($m['message'])
              ){
                $r[] = $this->getFormatedData($m['message']);
            }
        }
        $newMsg = $this->getMessage();
        foreach($newMsg['result'] as $m){
            if(in_array($m['message']['chat']['id'],$this->getGroups()) &&
               $m['message']['date'] > $ts &&
               !$this->isInfo($m['message'])
              ){
                if($m['message']['date'] > $msg['result'][sizeof($msg['result'])-1]['message']['date']){
                     //$r[] = $this->getFormatedData($m['message']);
                    //$msg['result'][] = $m;
                }
            }
        }
        $this->setOffset($msg['result'][sizeof($msg['result'])-1]['update_id']);
        $this->saveInFile($msg['result']);
        return array('posts' => $r);
        //print_r($r);
        //return $this->render('TestBundle:Default:index.html.twig',array('posts' => $r));
        
    }
    private function isInfo($m)
    {
            if(isset($m['text'])){
                preg_match_all("/(#\w+)/", $m['text'], $matches);
                if(in_array('#info', $matches[0]))
                   return true;
                else
                   return false;
            }
            else
            {
                return false;   
            }
            
    }
    
    private function getFormatedData($data)
    {
        $data = $this->setColor($data);
        
        if(isset($data['text'])){
            if(preg_match('/[\x{1F600}-\x{1F64F}]/u',$data['text']))
                $data['big'] = true; 
        }
        if(isset($data['photo'])){
            if(isset($data['photo'])){ 
                $data['url'] = $this->getFilePath($data['photo'][2]['file_id']);
                $data['type'] = 'img';
            }
        }
        if(isset($data['document'])){
            if($data['document']['mime_type'] == "image/png" || $data['document']['mime_type'] == "image/jpeg"){
                $data['type'] = 'img';
                $data['url'] = $this->getFilePath($data['document']['file_id']);
            }
            else{
                $data['type'] = 'file';
                $data['url'] = $this->getFilePath($data['document']['file_id']);
            }
        }
        if(isset($data['voice'])){
                $data['type'] = 'audio';
                $data['url'] = $this->getFilePath($data['voice']['file_id']);
        }
        if(isset($data['video'])){
                $data['type'] = 'video';
                $data['url'] = $this->getFilePath($data['video']['file_id']);
        }
        if(isset($data['location'])){
                $adress = $this->getFromUrl('http://maps.googleapis.com/maps/api/geocode/json?latlng='.$data['location']['latitude'].','.$data['location']['longitude'].'34&sensor=true');
                if(isset($adress['results'][0]['address_components'][1]['long_name']))
                    $adress = $adress['results'][0]['address_components'][1]['long_name'];
                else
                    $adress = $data['location']['longitude'].' — '.$data['location']['latitude'];
                $data['poi'] = array('id'=>$data['message_id'],'lon'=>$data['location']['longitude'],'lat'=>$data['location']['latitude'],'adress' => $adress);
        }
        
        return $data;
    }
    
    private function setColor($data){
        if(!isset($this->user[$data['from']['id']])){
            $this->user[$data['from']['id']] = "rgb(".rand($this->colorMin,$this->colorMax).",".rand($this->colorMin,$this->colorMax).",".rand($this->colorMin, $this->colorMax).")";
        }
        $data['from']['color'] = $this->user[$data['from']['id']];
        
        return $data;
    }
    
    private function setOffset($id){
        $url = "https://api.telegram.org/bot182843452:AAGnjG7L4ZA_c_MzHwBjYVY_dO_eyGyYdpU/getupdates?offset=".$id;
        return $this->getFromUrl($url);
    }
    
    private function getMessage()
    {
        $url = "https://api.telegram.org/bot182843452:AAGnjG7L4ZA_c_MzHwBjYVY_dO_eyGyYdpU/getupdates";
        return $this->getFromUrl($url);
    }
    
    private function getFilePath($id){
        $url = "https://api.telegram.org/bot182843452:AAGnjG7L4ZA_c_MzHwBjYVY_dO_eyGyYdpU/getFile?file_id=".$id; 
        return "https://api.telegram.org/file/bot182843452:AAGnjG7L4ZA_c_MzHwBjYVY_dO_eyGyYdpU/".$this->getFromUrl($url)['result']['file_path'];
    }
    
    public function getFromFile(){ 
        $file = 'data.json';
        $current = file_get_contents($file);
        return json_decode($current, true);
    }
    public function saveInFile($msg){
        //$msg = $this->getMessage()['result'];
        
        $file = 'data.json';
        
        file_put_contents($file, json_encode($msg, true));
        return true;
    }
    
    private function getFromUrl($url){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        curl_close($ch);
        $json = json_decode($output, true);
        
        return $json;
    }
    
    private function getGroups()
    {
        return array("-109125612");
    }
}
