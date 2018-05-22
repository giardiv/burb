<?php

namespace TelegramBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use TelegramBundle\Controller\FrontController;

/**
 * Message
 *
 * @ORM\Table(name="message")
 * @ORM\Entity(repositoryClass="TelegramBundle\Repository\MessageRepository")
 */
class Message
{
    private $persistedType = ["text","photo","video","voice","location","document","info"];

    function __construct($update,$fluxed){
        $this->setTid($update->update_id);

        $message = $update->message;

        $date = new \DateTime();
        $this->setDate($date->setTimestamp($message->date));

        $this->setSave(json_encode($update));
        $this->setType('empty');
        $this->setActive(true);

        if(!$fluxed) {
            $this->setType('fluxed');
            if (isset($message->new_chat_participant)) {
                $this->setType("new_participant");
                if ($message->chat->id == Flux::ADMINID) {
                    $this->setType("new_admin");
                }
                if ($message->group_chat_created == "true" || $message->group_chat_created == true || ($message->new_chat_participant->id == User::BOTID && User::isAdmin($message->from->id))) {
                    $this->setType("new_group");
                }
            }
        }else {
            if (isset($message->text)) {
                $this->setType("text");
                $hashtag = $this->getHashtag($message->text);
                if($hashtag !== null){
                    if($hashtag == "info"){
                        $this->setType("info");
                        $this->setActive(false);
                    }else {
                        $this->setType("updateFlux");
                    }
                }
                $this->setText($message->text);
                $this->parseLink();
            }
            if (isset($message->photo)){
                $this->setType("photo");
            }
            if (isset($message->video)){
                $this->setType("video");
            }
            if (isset($message->voice)){
                $this->setType("voice");
            }
            if (isset($message->location)){
                $this->setType("location");
                $this->setLat($message->location->latitude);
                $this->setLon($message->location->longitude);
            }
            if (isset($message->document)){
                $this->setType("document");
            }
        }
    }

    public function isPersistedType(){
        return in_array($this->getType(),$this->persistedType);
    }

    public static function getHashtag($string){
        $hash = null;
        preg_match("/#(\\w+)/", $string, $matches);
        if(isset($matches[1])) {
            $hash = $matches[1];
        }
        return $hash;
    }

    private function parseLink(){
        $text = $this->getText();
        $motif = "/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i";
        if(preg_match_all($motif, $text, $url)){
            foreach ($url[0] as $u) {
                $data = file_get_contents($u);
                if($titleMatch = preg_match('/<title[^>]*>(.*?)<\/title>/ims', $data, $matches)){
                    $title = "[". $matches[1] ."](". $u .")";
                }else{
                    $parse = parse_url($u);
                    $title = "[". $parse['host'] ."](". $u .")";
                }
                $text = str_replace($u,$title,$text);
            }
        }
        $this->setText(utf8_encode($text));
    }

    /**
     * @ORM\ManyToOne(targetEntity="TelegramBundle\Entity\Flux")
     * @ORM\JoinColumn(nullable=false)
     */
    private $flux;

    /**
     * @ORM\ManyToOne(targetEntity="TelegramBundle\Entity\User")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;


    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="tid", type="bigint", unique=true)
     */
    private $tid;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255)
     */
    private $type;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @var string
     *
     * @ORM\Column(name="text", type="text", nullable=true)
     */
    private $text;


    /**
     * @var string
     *
     * @ORM\Column(name="url", type="string", length=255, nullable=true)
     */
    private $url;

    /**
     * @var string
     *
     * @ORM\Column(name="lat", type="string", length=100, nullable=true)
     */
    private $lat;

    /**
     * @var string
     *
     * @ORM\Column(name="lon", type="string", length=100, nullable=true)
     */
    private $lon;

    /**
     * @var string
     *
     * @ORM\Column(name="save", type="text", nullable=true)
     */
    private $save;

    /**
     * @var string
     *
     * @ORM\Column(name="locality", type="text", nullable=true)
     */
    private $locality;


    /**
     * @var bool
     *
     * @ORM\Column(name="active", type="boolean", nullable=true)
     */
    private $active;


    public function setFlux(Flux $flux)
    {
        $this->flux = $flux;

        return $this;
    }

    /**
     * Get flux
     *
     * @return Flux
     */
    public function getFlux()
    {
        return $this->flux;
    }

    public function setUser(User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set tid
     *
     * @param integer $tid
     *
     * @return Message
     */
    public function setTid($tid)
    {
        $this->tid = $tid;

        return $this;
    }

    /**
     * Get tid
     *
     * @return int
     */
    public function getTid()
    {
        return $this->tid;
    }

    /**
     * Set type
     *
     * @param string $type
     *
     * @return Message
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return Message
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set text
     *
     * @param string $text
     *
     * @return Message
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Get text
     *
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Set url
     *
     * @param string $url
     *
     * @return Message
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set lat
     *
     * @param string $lat
     *
     * @return Message
     */
    public function setLat($lat)
    {
        $this->lat = $lat;

        return $this;
    }

    /**
     * Get lat
     *
     * @return string
     */
    public function getLat()
    {
        return $this->lat;
    }

    /**
     * Set lon
     *
     * @param string $lon
     *
     * @return Message
     */
    public function setLon($lon)
    {
        $this->lon = $lon;

        return $this;
    }

    /**
     * Get lon
     *
     * @return string
     */
    public function getLon()
    {
        return $this->lon;
    }

    /**
     * Set save
     *
     * @param string $save
     *
     * @return Message
     */
    public function setSave($save)
    {
        $this->save = $save;

        return $this;
    }

    /**
     * Get save
     *
     * @return string
     */
    public function getSave()
    {
        return $this->save;
    }

    /**
     * Set locality
     *
     * @param string $locality
     *
     * @return Message
     */
    public function setLocality($locality)
    {
        $this->locality = $locality;

        return $this;
    }

    /**
     * Get locality
     *
     * @return string
     */
    public function getLocality()
    {
        return str_replace("'", "’",  $this->locality);
    }

    /**
     * Set active
     *
     * @param boolean $active
     *
     * @return Flux
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active
     *
     * @return bool
     */
    public function getActive()
    {
        return $this->active;
    }

}
